<?php

declare(strict_types=1);

namespace App\Modules\NewsFeed\Services;

use App\Modules\NewsFeed\Jobs\ImportNewsSource;
use App\Modules\NewsFeed\Models\NewsSource;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class NewsFeedImportDispatcher
{
    /** @return array{batch: Batch|null, sources: int} */
    public function dispatch(?int $sourceId = null): array
    {
        $sourceIds = NewsSource::query()
            ->where('is_active', true)
            ->when($sourceId !== null, fn ($query) => $query->whereKey($sourceId))
            ->orderBy('id')
            ->pluck('id');

        if ($sourceIds->isEmpty()) {
            return ['batch' => null, 'sources' => 0];
        }

        $jobs = $sourceIds
            ->map(fn ($id) => new ImportNewsSource((int) $id))
            ->all();

        $batch = Bus::batch($jobs)
            ->name($sourceId === null ? 'News feed: all sources' : "News feed: source {$sourceId}")
            ->allowFailures()
            ->onQueue('news-feed')
            ->dispatch();

        return ['batch' => $batch, 'sources' => $sourceIds->count()];
    }
}
