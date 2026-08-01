<?php

declare(strict_types=1);

namespace App\Modules\Religions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderImplementation extends Model
{
    use HasFactory;

    protected $table = 'reminder_implementations';

    protected $fillable = [
        'reminder_type_id',
        'implementation_key',
        'implementation_mode',
        'handler_class',
        'config_json',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'config_json' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ReminderType::class, 'reminder_type_id');
    }
}
