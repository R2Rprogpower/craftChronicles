<?php

declare(strict_types=1);

namespace App\Modules\Religions\Models;

use App\Modules\Messenger\Models\MessengerGroupLink;
use App\Modules\Messenger\Models\MessengerUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGroupReligionPreference extends Model
{
    use HasFactory;

    protected $table = 'user_group_religion_preferences';

    protected $fillable = [
        'messenger_user_id',
        'messenger_group_link_id',
        'confession_id',
        'language_pack_id',
        'ritual_opt_in',
        'is_active',
        'meta_json',
    ];

    protected function casts(): array
    {
        return [
            'ritual_opt_in' => 'boolean',
            'is_active' => 'boolean',
            'meta_json' => 'array',
        ];
    }

    public function messengerUser(): BelongsTo
    {
        return $this->belongsTo(MessengerUser::class, 'messenger_user_id');
    }

    public function groupLink(): BelongsTo
    {
        return $this->belongsTo(MessengerGroupLink::class, 'messenger_group_link_id');
    }

    public function confession(): BelongsTo
    {
        return $this->belongsTo(Confession::class, 'confession_id');
    }

    public function languagePack(): BelongsTo
    {
        return $this->belongsTo(LanguagePack::class, 'language_pack_id');
    }
}
