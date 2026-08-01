<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Services\Handlers;

use App\Modules\Telegram\Contracts\TelegramCommandHandlerInterface;
use App\Modules\Telegram\DTO\TelegramCommandContext;

class EchoReminderContentHandler implements TelegramCommandHandlerInterface
{
    public function handle(TelegramCommandContext $context): string
    {
        if ($context->reminder !== null && ! empty($context->reminder->content_text)) {
            return (string) $context->reminder->content_text;
        }

        $title = $context->command->name !== '' ? $context->command->name : $context->command->command_key;

        return "Command '{$title}' executed.";
    }
}
