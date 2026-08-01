<?php

declare(strict_types=1);

namespace App\Modules\Messenger\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessengerBot extends Model
{
    use HasFactory;

    protected $table = 'messenger_bots';

    protected $fillable = [
        'name',
        'driver',
        'bot_token',
        'external_bot_id',
        'username',
        'is_active',
        'meta_json',
    ];

    protected function casts(): array
    {
        return [
            'bot_token' => 'encrypted',
            'is_active' => 'boolean',
            'meta_json' => 'array',
        ];
    }

    public function groups(): HasMany
    {
        return $this->hasMany(MessengerGroupLink::class, 'messenger_bot_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(MessengerUpdate::class, 'messenger_bot_id');
    }
}
