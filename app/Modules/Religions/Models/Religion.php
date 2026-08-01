<?php

declare(strict_types=1);

namespace App\Modules\Religions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Religion extends Model
{
    use HasFactory;

    protected $table = 'religions';

    protected $fillable = ['name', 'slug', 'description', 'description_format', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function confessions(): HasMany
    {
        return $this->hasMany(Confession::class, 'religion_id');
    }
}
