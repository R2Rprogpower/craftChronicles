<?php

declare(strict_types=1);

namespace App\Modules\NewsFeed\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $news_source_id
 * @property string $title
 * @property string $url
 * @property string $url_hash
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon $discovered_at
 * @property NewsSource $source
 */
class NewsArticle extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'news_source_id',
        'title',
        'url',
        'url_hash',
        'published_at',
        'discovered_at',
    ];

    /** @return BelongsTo<NewsSource, $this> */
    public function source(): BelongsTo
    {
        return $this->belongsTo(NewsSource::class, 'news_source_id');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'discovered_at' => 'datetime',
        ];
    }
}
