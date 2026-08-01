<?php

declare(strict_types=1);

namespace App\Modules\Religions\Models;

use App\Modules\Messenger\Models\MessengerGroupLink;
use App\Modules\Messenger\Models\MessengerUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderDeliveryTarget extends Model
{
    use HasFactory;

    protected $table = 'reminder_delivery_targets';

    protected $fillable = [
        'religion_reminder_id',
        'target_type',
        'messenger_group_link_id',
        'messenger_user_id',
        'channel_key',
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

    public function reminder(): BelongsTo
    {
        return $this->belongsTo(ReligionReminder::class, 'religion_reminder_id');
    }

    public function groupLink(): BelongsTo
    {
        return $this->belongsTo(MessengerGroupLink::class, 'messenger_group_link_id');
    }

    public function messengerUser(): BelongsTo
    {
        return $this->belongsTo(MessengerUser::class, 'messenger_user_id');
    }
}
