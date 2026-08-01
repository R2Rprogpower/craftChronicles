<?php

declare(strict_types=1);

namespace App\Modules\Religions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LanguagePack extends Model
{
    use HasFactory;

    protected $table = 'language_packs';

    protected $fillable = ['code', 'name', 'native_name', 'script', 'is_active', 'meta_json'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'meta_json' => 'array',
        ];
    }

    public function words(): HasMany
    {
        return $this->hasMany(LanguageWord::class, 'language_pack_id');
    }
}
