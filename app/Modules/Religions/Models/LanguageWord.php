<?php

declare(strict_types=1);

namespace App\Modules\Religions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LanguageWord extends Model
{
    use HasFactory;

    protected $table = 'language_words';

    protected $fillable = [
        'language_pack_id',
        'word',
        'translation',
        'transcription',
        'phrases',
        'meta_json',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'meta_json' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function languagePack(): BelongsTo
    {
        return $this->belongsTo(LanguagePack::class, 'language_pack_id');
    }
}
