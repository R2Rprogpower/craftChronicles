<?php

declare(strict_types=1);

use App\Modules\Telegram\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('telegram')->group(function (): void {
    Route::post('/webhook/{botId}', [TelegramWebhookController::class, 'handle'])
        ->whereNumber('botId')
        ->name('telegram.webhook.handle');
});
