<?php

declare(strict_types=1);

namespace App\Modules\Religions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReligionReminder extends Model
{
    use HasFactory;

    protected $table = 'religion_reminders';

    protected $fillable = [
        'confession_id',
        'ritual_id',
        'reminder_type_id',
        'implementation_id',
        'command_id',
        'language_pack_id',
        'word_mode',
        'title',
        'content_text',
        'text_format',
        'frequency_mode',
        'schedule_preset',
        'interval_minutes',
        'schedule_time',
        'schedule_timezone',
        'schedule_weekday',
        'schedule_monthday',
        'schedule_year_month',
        'schedule_year_day',
        'frequency_value',
        'command_trigger',
        'meta_json',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'meta_json' => 'array',
            'interval_minutes' => 'integer',
            'schedule_monthday' => 'integer',
            'schedule_year_month' => 'integer',
            'schedule_year_day' => 'integer',
            'word_mode' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function confession(): BelongsTo
    {
        return $this->belongsTo(Confession::class, 'confession_id');
    }

    public function ritual(): BelongsTo
    {
        return $this->belongsTo(Ritual::class, 'ritual_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ReminderType::class, 'reminder_type_id');
    }

    public function implementation(): BelongsTo
    {
        return $this->belongsTo(ReminderImplementation::class, 'implementation_id');
    }

    public function command(): BelongsTo
    {
        return $this->belongsTo(ReligionCommand::class, 'command_id');
    }

    public function languagePack(): BelongsTo
    {
        return $this->belongsTo(LanguagePack::class, 'language_pack_id');
    }
}
