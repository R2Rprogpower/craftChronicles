<?php

declare(strict_types=1);

namespace App\Modules\NewsFeed\Services;

use App\Modules\NewsFeed\Models\NewsArticle;
use App\Modules\NewsFeed\Models\NewsSource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class NewsFeedImporter
{
    public function __construct(private readonly NewsFeedReader $reader) {}

    /** @return array{sources: int, successful: int, added: int, errors: int} */
    public function import(?int $sourceId = null): array
    {
        /** @var Builder<NewsSource> $query */
        $query = NewsSource::query()->where('is_active', true)->orderBy('id');

        if ($sourceId !== null) {
            $query->whereKey($sourceId);
        }

        $result = ['sources' => 0, 'successful' => 0, 'added' => 0, 'errors' => 0];

        /** @var Collection<int, NewsSource> $sources */
        $sources = $query->get();

        foreach ($sources as $source) {
            $result['sources']++;

            try {
                $result['added'] += $this->importSource($source);
                $result['successful']++;
            } catch (Throwable $exception) {
                $source->forceFill([
                    'last_fetched_at' => now(),
                    'last_error' => mb_substr($exception->getMessage(), 0, 1000),
                ])->save();
                $result['errors']++;
            }
        }

        return $result;
    }

    public function importSource(NewsSource $source): int
    {
        $feed = $this->reader->read($source->site_url, $source->feed_url);
        $added = 0;
        $oldestAllowed = now()->subDays(90);

        foreach ($feed['items'] as $item) {
            if ($item['published_at'] !== null && $item['published_at']->lt($oldestAllowed)) {
                continue;
            }

            $url = $this->normalizeUrl($item['url']);

            if ($url === '') {
                continue;
            }

            $article = NewsArticle::query()->firstOrCreate(
                ['url_hash' => hash('sha256', $url)],
                [
                    'news_source_id' => $source->id,
                    'title' => mb_substr($item['title'], 0, 600),
                    'url' => $url,
                    'published_at' => $item['published_at'],
                    'discovered_at' => now(),
                ],
            );

            if ($article->wasRecentlyCreated) {
                $added++;
            }
        }

        $source->forceFill([
            'feed_url' => $feed['feed_url'],
            'last_fetched_at' => now(),
            'last_success_at' => now(),
            'last_error' => null,
        ])->save();

        return $added;
    }

    private function normalizeUrl(string $url): string
    {
        $url = trim($url);
        $fragmentPosition = strpos($url, '#');

        if ($fragmentPosition !== false) {
            $url = substr($url, 0, $fragmentPosition);
        }

        return filter_var($url, FILTER_VALIDATE_URL) === false ? '' : $url;
    }
}
