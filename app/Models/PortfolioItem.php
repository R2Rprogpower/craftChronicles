<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioItem extends Model
{
    protected $fillable = ['title', 'slug', 'category', 'status', 'short_description', 'description', 'technologies', 'links', 'timeline', 'achievements', 'metrics', 'media', 'featured', 'sort_order', 'active'];

    protected function casts(): array
    {
        return ['technologies' => 'array', 'links' => 'array', 'achievements' => 'array', 'metrics' => 'array', 'media' => 'array', 'featured' => 'boolean', 'active' => 'boolean'];
    }
}
