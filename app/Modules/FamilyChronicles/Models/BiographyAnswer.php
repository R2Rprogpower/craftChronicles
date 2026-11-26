<?php

declare(strict_types=1);

namespace App\Modules\FamilyChronicles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $family_person_id
 * @property string $question_key
 * @property string $status
 * @property string|null $text
 * @property \Illuminate\Support\Carbon|null $answered_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read FamilyPerson $person
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BiographyAttachment> $attachments
 */
class BiographyAnswer extends Model
{
    /** @var list<string> */
    protected $fillable = ['user_id', 'family_person_id', 'question_key', 'status', 'text', 'answered_at'];

    protected function casts(): array
    {
        return ['answered_at' => 'datetime'];
    }

    /** @return BelongsTo<FamilyPerson, $this> */
    public function person(): BelongsTo
    {
        return $this->belongsTo(FamilyPerson::class, 'family_person_id');
    }

    /** @return HasMany<BiographyAttachment, $this> */
    public function attachments(): HasMany
    {
        return $this->hasMany(BiographyAttachment::class);
    }
}
