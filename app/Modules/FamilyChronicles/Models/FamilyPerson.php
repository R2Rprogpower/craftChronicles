<?php

declare(strict_types=1);

namespace App\Modules\FamilyChronicles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property int|null $birth_year
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $relationship
 * @property string|null $gender
 * @property string|null $birth_place
 * @property string|null $residence
 * @property string|null $occupation
 * @property string|null $profile_notes
 * @property bool $relatives_complete
 * @property string $locale
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BiographyAnswer> $answers
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FamilyRelative> $relatives
 */
class FamilyPerson extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'name',
        'birth_year',
        'birth_date',
        'relationship',
        'gender',
        'birth_place',
        'residence',
        'occupation',
        'profile_notes',
        'relatives_complete',
        'locale',
    ];

    protected function casts(): array
    {
        return [
            'birth_year' => 'integer',
            'birth_date' => 'date:Y-m-d',
            'relatives_complete' => 'boolean',
        ];
    }

    /** @return BelongsTo<\App\Models\User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /** @return HasMany<BiographyAnswer, $this> */
    public function answers(): HasMany
    {
        return $this->hasMany(BiographyAnswer::class);
    }

    /** @return HasMany<FamilyRelative, $this> */
    public function relatives(): HasMany
    {
        return $this->hasMany(FamilyRelative::class);
    }
}
