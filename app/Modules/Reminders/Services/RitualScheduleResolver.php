<?php

declare(strict_types=1);

namespace App\Modules\Reminders\Services;

use App\Modules\Telegram\Contracts\TelegramCommandHandlerInterface;
use App\Modules\Telegram\DTO\TelegramCommandContext;

class RitualScheduleResolver implements TelegramCommandHandlerInterface
{
    public function handle(TelegramCommandContext $context): string
    {
        $title = $context->reminder?->title ?? 'Ritual Reminder';
        $body = trim((string) ($context->reminder?->content_text ?? 'No ritual reminder content configured yet.'));

        return "{$title}\n\n{$body}";
    }
}
