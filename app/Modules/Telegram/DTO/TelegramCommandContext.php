<?php

declare(strict_types=1);

namespace App\Modules\Telegram\DTO;

use App\Modules\Messenger\Models\MessengerBot;
use App\Modules\Messenger\Models\MessengerGroupLink;
use App\Modules\Messenger\Models\MessengerUser;
use App\Modules\Religions\Models\ReligionCommand;
use App\Modules\Religions\Models\ReligionReminder;
use App\Modules\Religions\Models\UserGroupReligionPreference;

class TelegramCommandContext
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, string>  $args
     */
    public function __construct(
        public readonly MessengerBot $bot,
        public readonly string $chatId,
        public readonly string $chatType,
        public readonly string $messageText,
        public readonly string $trigger,
        public readonly array $args,
        public readonly MessengerUser $messengerUser,
        public readonly ?MessengerGroupLink $groupLink,
        public readonly ?UserGroupReligionPreference $preference,
        public readonly ReligionCommand $command,
        public readonly ?ReligionReminder $reminder,
        public readonly array $payload,
    ) {}
}
