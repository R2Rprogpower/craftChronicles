<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentItem extends Model
{
    protected $fillable = ['title', 'slug', 'channel', 'status', 'description', 'url', 'published_at', 'metadata', 'featured', 'sort_order', 'active'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'metadata' => 'array', 'featured' => 'boolean', 'active' => 'boolean'];
    }
}
