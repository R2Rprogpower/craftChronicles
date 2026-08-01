<?php

declare(strict_types=1);

namespace App\Modules\Religions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReligionCommand extends Model
{
    use HasFactory;

    protected $table = 'religion_commands';

    protected $fillable = ['confession_id', 'command_key', 'trigger', 'name', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function confession(): BelongsTo
    {
        return $this->belongsTo(Confession::class, 'confession_id');
    }
}
