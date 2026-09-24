<?php

declare(strict_types=1);

namespace App\Modules\MovieTierList\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property array{movies?: list<array<string, mixed>>} $payload
 * @property int $revision
 * @property Carbon|null $updated_at
 */
class MovieTierList extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'slug',
        'payload',
        'revision',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'revision' => 'integer',
        ];
    }
}
