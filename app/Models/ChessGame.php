<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string|null $white_profile_id
 * @property string|null $black_profile_id
 * @property string $fen
 * @property string $status
 * @property string|null $result
 * @property array<int, string> $moves
 * @property-read ChessProfile|null $white
 * @property-read ChessProfile|null $black
 */
class ChessGame extends Model
{
    use HasUuids;

    protected $fillable = ['white_profile_id', 'black_profile_id', 'fen', 'status', 'result', 'moves'];

    protected function casts(): array
    {
        return ['moves' => 'array'];
    }

    /** @return BelongsTo<ChessProfile, $this> */
    public function white(): BelongsTo
    {
        return $this->belongsTo(ChessProfile::class, 'white_profile_id');
    }

    /** @return BelongsTo<ChessProfile, $this> */
    public function black(): BelongsTo
    {
        return $this->belongsTo(ChessProfile::class, 'black_profile_id');
    }
}
