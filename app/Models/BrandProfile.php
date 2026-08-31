<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandProfile extends Model
{
    protected $fillable = ['key', 'name', 'headline', 'bio', 'location', 'availability', 'expertise', 'social_links', 'contact', 'faq', 'active'];

    protected function casts(): array
    {
        return ['expertise' => 'array', 'social_links' => 'array', 'contact' => 'array', 'faq' => 'array', 'active' => 'boolean'];
    }
}
