<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOffering extends Model
{
    protected $table = 'service_offerings';

    protected $fillable = ['name', 'slug', 'description', 'benefits', 'engagement_format', 'cta_label', 'active', 'sort_order'];

    protected function casts(): array
    {
        return ['benefits' => 'array', 'active' => 'boolean'];
    }
}
