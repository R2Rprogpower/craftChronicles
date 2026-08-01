<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Http\Controllers;

use App\Core\Responses\SuccessResponse;
use App\Http\Controllers\Controller;
use App\Modules\Messenger\Models\MessengerBot;
use App\Modules\Telegram\Services\TelegramWebhookService;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function __construct(
        private readonly TelegramWebhookService $webhookService
    ) {}

    public function handle(Request $request, int $botId): SuccessResponse
    {
        $bot = MessengerBot::query()
            ->where('id', $botId)
            ->where('driver', 'telegram')
            ->where('is_active', true)
            ->firstOrFail();

        $payload = $request->json()->all();
        if (! is_array($payload)) {
            $payload = [];
        }

        $this->webhookService->ingest($bot, $payload);

        return new SuccessResponse([
            'ok' => true,
        ]);
    }
}
