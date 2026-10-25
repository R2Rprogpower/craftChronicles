<?php

declare(strict_types=1);

namespace App\Modules\NewsFeed\Jobs;

use App\Modules\NewsFeed\Models\NewsSource;
use App\Modules\NewsFeed\Services\NewsFeedImporter;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ImportNewsSource implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public int $tries = 2;

    public bool $failOnTimeout = true;

    /** @var list<int> */
    public array $backoff = [30];

    public function __construct(public readonly int $sourceId) {}

    /** @return list<WithoutOverlapping> */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping("news-feed-source:{$this->sourceId}"))
                ->releaseAfter(30)
                ->expireAfter(150),
        ];
    }

    public function handle(NewsFeedImporter $importer): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $source = NewsSource::query()
            ->where('is_active', true)
            ->find($this->sourceId);

        if (! $source instanceof NewsSource) {
            return;
        }

        $importer->importSource($source);
    }

    public function failed(?Throwable $exception): void
    {
        NewsSource::query()->whereKey($this->sourceId)->update([
            'last_fetched_at' => now(),
            'last_error' => mb_substr($exception?->getMessage() ?? 'Фоновый импорт завершился с ошибкой.', 0, 1000),
        ]);
    }
}
