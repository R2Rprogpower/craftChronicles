<?php

declare(strict_types=1);

namespace App\Modules\NewsFeed\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\MovieTierList\Services\MovieTierListEditorAccess;
use App\Modules\NewsFeed\Http\Requests\StoreNewsSourceRequest;
use App\Modules\NewsFeed\Models\NewsArticle;
use App\Modules\NewsFeed\Models\NewsSource;
use App\Modules\NewsFeed\Models\NewsTopic;
use App\Modules\NewsFeed\Services\NewsFeedImportDispatcher;
use App\Modules\NewsFeed\Services\PublicFeedUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class NewsFeedController extends Controller
{
    public function index(Request $request, MovieTierListEditorAccess $access): View
    {
        $period = (string) $request->query('period', '12h');
        $since = match ($period) {
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            'all' => null,
            default => now()->subHours(12),
        };

        $articles = NewsArticle::query()
            ->with(['source.topic'])
            ->when($request->filled('topic'), function ($query) use ($request): void {
                $query->whereHas('source.topic', fn ($topicQuery) => $topicQuery->where('slug', $request->query('topic')));
            })
            ->when($request->filled('source'), fn ($query) => $query->where('news_source_id', $request->integer('source')))
            ->when($request->filled('language'), function ($query) use ($request): void {
                $query->whereHas('source', fn ($sourceQuery) => $sourceQuery->where('language', $request->query('language')));
            })
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = mb_substr(trim((string) $request->query('q')), 0, 120);
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($since !== null, function ($query) use ($since): void {
                $query->where(function ($dateQuery) use ($since): void {
                    $dateQuery->where('published_at', '>=', $since)
                        ->orWhere(function ($missingPublishedAt) use ($since): void {
                            $missingPublishedAt->whereNull('published_at')->where('discovered_at', '>=', $since);
                        });
                });
            })
            ->orderByRaw('COALESCE(published_at, discovered_at) DESC')
            ->paginate(80)
            ->withQueryString();

        return view('news-feed.index', [
            'articles' => $articles,
            'topics' => NewsTopic::query()->where('is_active', true)->orderBy('position')->get(),
            'sources' => NewsSource::query()->with('topic')->where('is_active', true)->orderBy('name')->get(),
            'canEdit' => $access->canEdit($request),
            'period' => $period,
            'totalArticles' => NewsArticle::query()->count(),
            'recentArticles' => NewsArticle::query()
                ->where(fn ($query) => $query->where('published_at', '>=', now()->subHours(12))
                    ->orWhere(fn ($missing) => $missing->whereNull('published_at')->where('discovered_at', '>=', now()->subHours(12))))
                ->count(),
            'lastUpdatedAt' => NewsSource::query()->max('last_success_at'),
        ]);
    }

    public function store(
        StoreNewsSourceRequest $request,
        PublicFeedUrl $urlGuard,
    ): RedirectResponse {
        $validated = $request->validated();

        try {
            $urlGuard->assertSafe($validated['site_url']);

            if (is_string($validated['feed_url'] ?? null)) {
                $urlGuard->assertSafe($validated['feed_url']);
            }
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['site_url' => $exception->getMessage()]);
        }

        NewsSource::query()->create([...$validated, 'is_active' => true]);

        return back()->with('status', 'Источник добавлен. Нажми «Обновить», чтобы импортировать публикации.');
    }

    public function refresh(Request $request, NewsFeedImportDispatcher $dispatcher): RedirectResponse
    {
        $validated = $request->validate([
            'source_id' => ['nullable', 'integer', 'exists:news_sources,id'],
        ]);
        $result = $dispatcher->dispatch(isset($validated['source_id']) ? (int) $validated['source_id'] : null);

        return back()->with(
            'status',
            $result['sources'] === 0
                ? 'Нет активных источников для обновления.'
                : sprintf(
                    'Фоновое обновление запущено: %d источник(ов), batch %s. Страницу можно закрыть.',
                    $result['sources'],
                    $result['batch']->id,
                ),
        );
    }
}
