<?php

declare(strict_types=1);

namespace App\Modules\Religions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Confession extends Model
{
    use HasFactory;

    protected $table = 'confessions';

    protected $fillable = ['religion_id', 'name', 'slug', 'description', 'description_format', 'welcome_message', 'language_pack_id', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    public function rituals(): HasMany
    {
        return $this->hasMany(Ritual::class, 'confession_id');
    }

    public function languagePack(): BelongsTo
    {
        return $this->belongsTo(LanguagePack::class, 'language_pack_id');
    }
}
