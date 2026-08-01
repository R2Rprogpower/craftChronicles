<?php

declare(strict_types=1);

namespace App\Modules\Messenger\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessengerUser extends Model
{
    use HasFactory;

    protected $table = 'messenger_users';

    protected $fillable = [
        'driver',
        'external_user_id',
        'username',
        'display_name',
        'language_code',
        'meta_json',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'meta_json' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
