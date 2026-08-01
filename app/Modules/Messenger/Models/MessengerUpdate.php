<?php

declare(strict_types=1);

namespace App\Modules\Messenger\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessengerUpdate extends Model
{
    use HasFactory;

    protected $table = 'messenger_updates';

    protected $fillable = [
        'messenger_bot_id',
        'driver',
        'external_update_id',
        'payload_json',
    ];

    protected function casts(): array
    {
        return [
            'payload_json' => 'array',
        ];
    }

    public function bot(): BelongsTo
    {
        return $this->belongsTo(MessengerBot::class, 'messenger_bot_id');
    }
}
