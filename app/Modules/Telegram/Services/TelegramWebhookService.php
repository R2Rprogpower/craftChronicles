<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Services;

use App\Modules\Messenger\Models\MessengerBot;
use App\Modules\Messenger\Models\MessengerUpdate;
use App\Modules\Messenger\Services\BotOnboardingService;
use Illuminate\Support\Arr;
use Throwable;

class TelegramWebhookService
{
    public function __construct(
        private readonly BotOnboardingService $onboardingService,
        private readonly TelegramCommandDispatchService $commandDispatchService
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function ingest(MessengerBot $bot, array $payload): void
    {
        $updateId = Arr::get($payload, 'update_id');

        MessengerUpdate::query()->create([
            'messenger_bot_id' => $bot->id,
            'driver' => $bot->driver,
            'external_update_id' => is_scalar($updateId) ? (string) $updateId : null,
            'payload_json' => $payload,
        ]);

        try {
            $this->commandDispatchService->dispatchIfSupported($bot, $payload);
        } catch (Throwable) {
            // Webhook should stay resilient even if command dispatch fails.
        }

        $chat = Arr::get($payload, 'message.chat');

        if (! is_array($chat)) {
            return;
        }

        $chatType = (string) ($chat['type'] ?? '');
        if (! in_array($chatType, ['group', 'supergroup'], true)) {
            return;
        }

        $chatId = (string) ($chat['id'] ?? '');
        if ($chatId === '') {
            return;
        }

        $title = (string) ($chat['title'] ?? 'Telegram Group');
        $this->onboardingService->linkGroup($bot, $chatId, $title);
    }
}
