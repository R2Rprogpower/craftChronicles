<?php

use App\Modules\Messenger\Models\MessengerBot;
use App\Modules\Messenger\Models\MessengerUpdate;
use App\Modules\Messenger\Services\MessengerClientRegistry;
use App\Modules\Religions\Services\MorningRitualBroadcastService;
use App\Modules\Religions\Services\ReminderDispatchService;
use App\Modules\Telegram\Services\TelegramWebhookService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('religions:broadcast-morning-ritual', function (MorningRitualBroadcastService $service): void {
    $result = $service->broadcastDaily();

    $this->info(sprintf(
        'Morning ritual broadcast complete. groups=%d messages=%d',
        $result['groups'],
        $result['messages']
    ));
})->purpose('Send daily morning ritual posts for religions with active followers in each group.');

Schedule::command('religions:broadcast-morning-ritual')->dailyAt('08:00');

Artisan::command('religions:dispatch-due-reminders {--reminder-id= : Dispatch only this religion_reminders.id} {--force : Ignore due checks and dispatch now}', function (ReminderDispatchService $service): void {
    $reminderId = is_numeric((string) $this->option('reminder-id')) ? (int) $this->option('reminder-id') : null;
    $force = (bool) $this->option('force');

    $result = $service->dispatchDue($reminderId, $force);

    $this->info(sprintf(
        'Due reminder dispatch complete. checked=%d dispatched=%d messages=%d',
        $result['checked'],
        $result['dispatched'],
        $result['messages']
    ));
})->purpose('Dispatch active cron/interval religion reminders that are due.');

Schedule::command('religions:dispatch-due-reminders')->everyMinute()->withoutOverlapping(55);

Artisan::command(
    'telegram:poll {--bot-id= : Poll only this messenger_bots.id} {--sleep=1 : Seconds between pulls} {--limit=100 : Telegram getUpdates limit} {--max-loops=0 : Stop after N loops (0 = forever)}',
    function (MessengerClientRegistry $registry, TelegramWebhookService $webhookService): void {
        $botIdOption = $this->option('bot-id');
        $sleepSeconds = max(1, (int) $this->option('sleep'));
        $limit = max(1, min(100, (int) $this->option('limit')));
        $maxLoops = max(0, (int) $this->option('max-loops'));

        $this->warn('Temporary local polling mode enabled. Use Ctrl+C to stop.');

        $loop = 0;

        while (true) {
            $loop++;

            $botsQuery = MessengerBot::query()
                ->where('driver', 'telegram')
                ->where('is_active', true)
                ->orderBy('id');

            if (is_numeric((string) $botIdOption) && (int) $botIdOption > 0) {
                $botsQuery->where('id', (int) $botIdOption);
            }

            $bots = $botsQuery->get();

            if ($bots->isEmpty()) {
                $this->error('No active telegram bots found to poll.');

                return;
            }

            $totalIngested = 0;

            foreach ($bots as $bot) {
                try {
                    $maxSeenUpdateId = MessengerUpdate::query()
                        ->where('messenger_bot_id', $bot->id)
                        ->whereNotNull('external_update_id')
                        ->max('external_update_id');

                    $offset = is_numeric((string) $maxSeenUpdateId) ? ((int) $maxSeenUpdateId + 1) : null;

                    $updates = $registry
                        ->forDriver((string) $bot->driver)
                        ->getUpdates((string) $bot->bot_token, $offset, $limit);

                    foreach ($updates as $update) {
                        $webhookService->ingest($bot, $update);
                        $totalIngested++;
                    }
                } catch (\Throwable $exception) {
                    $this->warn(sprintf(
                        'Skipping bot #%d (%s): %s',
                        (int) $bot->id,
                        (string) $bot->name,
                        $exception->getMessage()
                    ));
                }
            }

            if ($totalIngested > 0) {
                $this->info(sprintf('Loop %d: ingested %d update(s).', $loop, $totalIngested));
            }

            if ($maxLoops > 0 && $loop >= $maxLoops) {
                $this->info('Reached max loops, exiting.');

                return;
            }

            usleep($sleepSeconds * 1000000);
        }
    }
)->purpose('Temporary local Telegram polling fallback when webhook is not configured.');
