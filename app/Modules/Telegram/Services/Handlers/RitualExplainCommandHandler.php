<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Services\Handlers;

use App\Modules\Religions\Models\ReligionReminder;
use App\Modules\Religions\Models\Ritual;
use App\Modules\Telegram\Contracts\TelegramCommandHandlerInterface;
use App\Modules\Telegram\DTO\TelegramCommandContext;

class RitualExplainCommandHandler implements TelegramCommandHandlerInterface
{
    public function handle(TelegramCommandContext $context): string
    {
        $action = strtolower((string) ($context->args[0] ?? ''));
        $ritualKey = strtolower((string) ($context->args[1] ?? ''));

        if ($action !== 'explain' || $ritualKey === '') {
            return 'Usage: /ritual explain <ritual-key>';
        }

        $confessionId = (int) ($context->preference?->confession_id ?? 0);
        if ($confessionId < 1) {
            return 'No confession preference found for this group user context.';
        }

        $ritual = Ritual::query()
            ->where('confession_id', $confessionId)
            ->where('ritual_key', $ritualKey)
            ->where('is_active', true)
            ->first();

        if ($ritual === null) {
            return "Ritual '{$ritualKey}' was not found for your current confession.";
        }

        $reminder = ReligionReminder::query()
            ->where('confession_id', $confessionId)
            ->where('ritual_id', $ritual->id)
            ->where('is_active', true)
            ->orderBy('id')
            ->first();

        $details = trim((string) ($ritual->description ?? 'No ritual description configured.'));

        if ($reminder === null) {
            return "Ritual: {$ritual->name} ({$ritual->ritual_key})\n\n{$details}";
        }

        $reminderInfo = "Linked reminder: {$reminder->title} [{$reminder->frequency_mode}]";

        return "Ritual: {$ritual->name} ({$ritual->ritual_key})\n\n{$details}\n\n{$reminderInfo}";
    }
}
