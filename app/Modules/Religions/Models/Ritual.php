<?php

declare(strict_types=1);

namespace App\Modules\Religions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ritual extends Model
{
    use HasFactory;

    protected $table = 'rituals';

    protected $fillable = ['confession_id', 'ritual_key', 'name', 'description', 'description_format', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function confession(): BelongsTo
    {
        return $this->belongsTo(Confession::class, 'confession_id');
    }
}
