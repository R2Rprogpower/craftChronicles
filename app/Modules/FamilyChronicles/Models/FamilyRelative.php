<?php

declare(strict_types=1);

namespace App\Modules\FamilyChronicles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $family_person_id
 * @property string $relationship
 * @property string|null $name
 * @property int|null $birth_year
 */
class FamilyRelative extends Model
{
    /** @var list<string> */
    protected $fillable = ['user_id', 'family_person_id', 'relationship', 'name', 'birth_year'];

    protected function casts(): array
    {
        return ['birth_year' => 'integer'];
    }

    /** @return BelongsTo<FamilyPerson, $this> */
    public function person(): BelongsTo
    {
        return $this->belongsTo(FamilyPerson::class, 'family_person_id');
    }
}
