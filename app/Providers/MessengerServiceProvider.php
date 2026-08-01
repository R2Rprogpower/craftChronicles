<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\Messenger\Services\MessengerClientRegistry;
use App\Modules\Telegram\Services\TelegramMessengerClient;
use Illuminate\Support\ServiceProvider;

class MessengerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MessengerClientRegistry::class, function ($app): MessengerClientRegistry {
            return new MessengerClientRegistry([
                'telegram' => $app->make(TelegramMessengerClient::class),
            ]);
        });
    }

    public function boot(): void
    {
        //
    }
}
