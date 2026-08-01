<?php

declare(strict_types=1);

namespace App\Modules\Religions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderRun extends Model
{
    use HasFactory;

    protected $table = 'reminder_runs';

    protected $fillable = [
        'religion_reminder_id',
        'reminder_delivery_target_id',
        'next_run_at_utc',
        'scheduled_for_utc',
        'sent_at',
        'status',
        'idempotency_key',
        'attempt_count',
        'last_error',
        'payload_json',
    ];

    protected function casts(): array
    {
        return [
            'next_run_at_utc' => 'datetime',
            'scheduled_for_utc' => 'datetime',
            'sent_at' => 'datetime',
            'attempt_count' => 'integer',
            'payload_json' => 'array',
        ];
    }

    public function reminder(): BelongsTo
    {
        return $this->belongsTo(ReligionReminder::class, 'religion_reminder_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(ReminderDeliveryTarget::class, 'reminder_delivery_target_id');
    }
}
