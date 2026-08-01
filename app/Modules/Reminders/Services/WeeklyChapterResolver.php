<?php

declare(strict_types=1);

namespace App\Modules\Reminders\Services;

use App\Modules\Telegram\Contracts\TelegramCommandHandlerInterface;
use App\Modules\Telegram\DTO\TelegramCommandContext;

class WeeklyChapterResolver implements TelegramCommandHandlerInterface
{
    public function handle(TelegramCommandContext $context): string
    {
        $title = $context->reminder?->title ?? 'Weekly Chapter';
        $body = trim((string) ($context->reminder?->content_text ?? 'Weekly chapter is prepared.'));

        return "{$title}\n\n{$body}";
    }
}
