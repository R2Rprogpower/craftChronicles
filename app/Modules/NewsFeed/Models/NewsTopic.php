<?php

declare(strict_types=1);

namespace App\Modules\NewsFeed\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property int $position
 * @property bool $is_active
 */
class NewsTopic extends Model
{
    /** @var list<string> */
    protected $fillable = ['slug', 'name', 'position', 'is_active'];

    /** @return HasMany<NewsSource, $this> */
    public function sources(): HasMany
    {
        return $this->hasMany(NewsSource::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
