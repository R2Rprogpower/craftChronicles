<?php

declare(strict_types=1);

namespace App\Modules\Religions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReminderType extends Model
{
    use HasFactory;

    protected $table = 'reminder_types';

    protected $fillable = ['type_key', 'name', 'description', 'is_builtin'];

    protected function casts(): array
    {
        return ['is_builtin' => 'boolean'];
    }

    public function implementations(): HasMany
    {
        return $this->hasMany(ReminderImplementation::class, 'reminder_type_id');
    }
}
