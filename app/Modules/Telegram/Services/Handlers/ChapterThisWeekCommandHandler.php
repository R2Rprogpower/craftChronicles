<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Services\Handlers;

use App\Modules\Telegram\Contracts\TelegramCommandHandlerInterface;
use App\Modules\Telegram\DTO\TelegramCommandContext;

class ChapterThisWeekCommandHandler implements TelegramCommandHandlerInterface
{
    public function handle(TelegramCommandContext $context): string
    {
        $firstArg = strtolower((string) ($context->args[0] ?? ''));
        if ($firstArg !== 'thisweek') {
            return 'Usage: /chapter thisweek';
        }

        $title = $context->reminder?->title ?? 'Weekly Chapter';
        $body = trim((string) ($context->reminder?->content_text ?? 'No weekly chapter content configured yet.'));

        return "{$title}\n\n{$body}";
    }
}
