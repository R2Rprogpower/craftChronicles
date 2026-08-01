<?php

declare(strict_types=1);

namespace App\Modules\Messenger\Services;

use App\Modules\Messenger\Models\MessengerBot;
use App\Modules\Messenger\Models\MessengerGroupLink;
use App\Modules\Messenger\Models\MessengerUpdate;
use Illuminate\Support\Arr;
use RuntimeException;

class BotOnboardingService
{
    public function __construct(
        private readonly MessengerClientRegistry $registry
    ) {}

    public function upsertBot(string $name, string $driver, string $token): MessengerBot
    {
        $identity = $this->registry->forDriver($driver)->getIdentity($token);
        $result = Arr::get($identity, 'result', []);

        /** @var MessengerBot $bot */
        $bot = MessengerBot::query()->updateOrCreate(
            [
                'driver' => $driver,
                'external_bot_id' => (string) Arr::get($result, 'id', ''),
            ],
            [
                'name' => $name,
                'bot_token' => $token,
                'username' => Arr::get($result, 'username'),
                'meta_json' => [
                    'first_name' => Arr::get($result, 'first_name'),
                    'can_join_groups' => Arr::get($result, 'can_join_groups', true),
                ],
                'is_active' => true,
            ]
        );

        return $bot;
    }

    public function linkGroup(MessengerBot $bot, string $externalChatId, ?string $title = null): MessengerGroupLink
    {
        /** @var MessengerGroupLink $link */
        $link = MessengerGroupLink::query()->updateOrCreate(
            [
                'messenger_bot_id' => $bot->id,
                'external_chat_id' => $externalChatId,
            ],
            [
                'title' => $title,
                'is_active' => true,
            ]
        );

        return $link;
    }

    public function relinkGroup(MessengerGroupLink $groupLink, string $externalChatId, ?string $title = null): MessengerGroupLink
    {
        $externalChatId = trim($externalChatId);

        if ($externalChatId === '') {
            throw new RuntimeException('New group chat id is required.');
        }

        $duplicate = MessengerGroupLink::query()
            ->where('messenger_bot_id', (int) $groupLink->messenger_bot_id)
            ->where('external_chat_id', $externalChatId)
            ->where('id', '!=', (int) $groupLink->id)
            ->first();

        if ($duplicate !== null) {
            throw new RuntimeException('This bot is already linked to the new group chat id. Use the existing linked row instead of relinking into a duplicate.');
        }

        $groupLink->external_chat_id = $externalChatId;
        $groupLink->title = $title !== null && trim($title) !== '' ? trim($title) : $groupLink->title;
        $groupLink->is_active = true;
        $groupLink->save();

        return $groupLink;
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function discoverGroupsFromUpdates(MessengerBot $bot): array
    {
        $maxSeenUpdateId = MessengerUpdate::query()
            ->where('messenger_bot_id', $bot->id)
            ->whereNotNull('external_update_id')
            ->max('external_update_id');

        $offset = is_numeric((string) $maxSeenUpdateId) ? ((int) $maxSeenUpdateId + 1) : null;

        $updates = $this->registry
            ->forDriver($bot->driver)
            ->getUpdates((string) $bot->bot_token, $offset, 100);

        $groups = [];

        foreach ($updates as $update) {
            $updateId = Arr::get($update, 'update_id');

            MessengerUpdate::query()->create([
                'messenger_bot_id' => $bot->id,
                'driver' => $bot->driver,
                'external_update_id' => is_scalar($updateId) ? (string) $updateId : null,
                'payload_json' => $update,
            ]);

            $chat = Arr::get($update, 'message.chat');

            if (! is_array($chat)) {
                continue;
            }

            $chatType = (string) ($chat['type'] ?? '');
            if (! in_array($chatType, ['group', 'supergroup'], true)) {
                continue;
            }

            $chatId = (string) ($chat['id'] ?? '');
            if ($chatId === '') {
                continue;
            }

            $title = (string) ($chat['title'] ?? 'Telegram Group');

            $this->linkGroup($bot, $chatId, $title);

            $groups[$chatId] = [
                'chat_id' => $chatId,
                'title' => $title,
            ];
        }

        return array_values($groups);
    }

    public function sendGroupTestMessage(MessengerBot $bot, string $externalChatId): void
    {
        $text = "Rebe bot connected successfully.\nYou can now configure religion, language packs, and reminders.";

        $this->registry
            ->forDriver($bot->driver)
            ->sendMessage((string) $bot->bot_token, $externalChatId, $text);
    }

    /**
     * @return array<string, mixed>
     */
    public function registerWebhook(MessengerBot $bot, string $url): array
    {
        return $this->registry
            ->forDriver($bot->driver)
            ->setWebhook((string) $bot->bot_token, $url);
    }
}
