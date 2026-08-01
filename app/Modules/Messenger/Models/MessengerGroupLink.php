<?php

declare(strict_types=1);

namespace App\Modules\Messenger\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessengerGroupLink extends Model
{
    use HasFactory;

    protected $table = 'messenger_group_links';

    protected $fillable = [
        'messenger_bot_id',
        'external_chat_id',
        'title',
        'is_active',
        'meta_json',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'meta_json' => 'array',
        ];
    }

    public function bot(): BelongsTo
    {
        return $this->belongsTo(MessengerBot::class, 'messenger_bot_id');
    }
}
