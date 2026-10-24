<?php

declare(strict_types=1);

namespace App\Modules\GratitudeVariants\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $language
 * @property string $body
 * @property list<string> $tags
 * @property bool $is_active
 */
class GratitudeVariant extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'title',
        'language',
        'body',
        'tags',
        'is_active',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
