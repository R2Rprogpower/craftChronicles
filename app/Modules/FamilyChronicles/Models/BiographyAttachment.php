<?php

declare(strict_types=1);

namespace App\Modules\FamilyChronicles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $biography_answer_id
 * @property string $original_name
 * @property string $path
 * @property string $mime_type
 * @property int $size
 * @property string|null $caption
 * @property-read BiographyAnswer $answer
 */
class BiographyAttachment extends Model
{
    /** @var list<string> */
    protected $fillable = ['user_id', 'biography_answer_id', 'original_name', 'path', 'mime_type', 'size', 'caption'];

    protected function casts(): array
    {
        return ['size' => 'integer'];
    }

    /** @return BelongsTo<BiographyAnswer, $this> */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(BiographyAnswer::class, 'biography_answer_id');
    }
}
