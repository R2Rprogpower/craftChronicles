<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'slug', 'stage', 'description', 'links', 'roadmap', 'featured', 'sort_order', 'active'];

    protected function casts(): array
    {
        return ['links' => 'array', 'roadmap' => 'array', 'featured' => 'boolean', 'active' => 'boolean'];
    }
}
