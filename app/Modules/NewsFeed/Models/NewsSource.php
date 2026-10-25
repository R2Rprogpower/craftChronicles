<?php

declare(strict_types=1);

namespace App\Modules\NewsFeed\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $news_topic_id
 * @property string $name
 * @property string $language
 * @property string $site_url
 * @property string|null $feed_url
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_fetched_at
 * @property \Illuminate\Support\Carbon|null $last_success_at
 * @property string|null $last_error
 * @property NewsTopic $topic
 */
class NewsSource extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'news_topic_id',
        'name',
        'language',
        'site_url',
        'feed_url',
        'is_active',
        'last_fetched_at',
        'last_success_at',
        'last_error',
    ];

    /** @return BelongsTo<NewsTopic, $this> */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(NewsTopic::class, 'news_topic_id');
    }

    /** @return HasMany<NewsArticle, $this> */
    public function articles(): HasMany
    {
        return $this->hasMany(NewsArticle::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_fetched_at' => 'datetime',
            'last_success_at' => 'datetime',
        ];
    }
}
