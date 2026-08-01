<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Contracts;

use App\Modules\Telegram\DTO\TelegramCommandContext;

interface TelegramCommandHandlerInterface
{
    public function handle(TelegramCommandContext $context): string;
}
