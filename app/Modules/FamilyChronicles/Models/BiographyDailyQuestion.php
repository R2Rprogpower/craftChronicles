<?php

declare(strict_types=1);

namespace App\Modules\FamilyChronicles\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $family_person_id
 * @property string $question_date
 * @property string $question_key
 */
class BiographyDailyQuestion extends Model
{
    /** @var list<string> */
    protected $fillable = ['user_id', 'family_person_id', 'question_date', 'question_key'];
}
