<?php

declare(strict_types=1);

namespace App\Modules\Religions\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReligionsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $religionId = $this->upsertReligion($now);
        $confessionId = $this->upsertConfession($religionId, $now);

        [$holidayTypeId, $ritualTypeId, $customTypeId] = $this->upsertReminderTypes($now);

        $implementations = $this->upsertImplementations(
            $holidayTypeId,
            $ritualTypeId,
            $customTypeId,
            $now
        );

        $rituals = $this->upsertRituals($confessionId, $now);
        $commands = $this->upsertCommands($confessionId, $now);

        $reminders = $this->upsertReminders(
            confessionId: $confessionId,
            rituals: $rituals,
            commands: $commands,
            holidayTypeId: $holidayTypeId,
            customTypeId: $customTypeId,
            implementations: $implementations,
            now: $now
        );

        $this->seedRuntimeBindings($confessionId, $reminders, $now);
    }

    private function upsertReligion(\Illuminate\Support\Carbon $now): int
    {
        $id = DB::table('religions')->where('slug', 'religion-1')->value('id');

        if ($id !== null) {
            DB::table('religions')->where('id', $id)->update([
                'name' => 'Religion 1',
                'description' => 'Seed religion root for confession/reminder graph.',
                'is_active' => true,
                'updated_at' => $now,
            ]);

            return (int) $id;
        }

        return (int) DB::table('religions')->insertGetId([
            'name' => 'Religion 1',
            'slug' => 'religion-1',
            'description' => 'Seed religion root for confession/reminder graph.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function upsertConfession(int $religionId, \Illuminate\Support\Carbon $now): int
    {
        $id = DB::table('confessions')
            ->where('religion_id', $religionId)
            ->where('slug', 'confession-1')
            ->value('id');

        if ($id !== null) {
            DB::table('confessions')->where('id', $id)->update([
                'name' => 'Confession 1',
                'description' => 'Seed confession branch under Religion 1.',
                'is_active' => true,
                'updated_at' => $now,
            ]);

            return (int) $id;
        }

        return (int) DB::table('confessions')->insertGetId([
            'religion_id' => $religionId,
            'name' => 'Confession 1',
            'slug' => 'confession-1',
            'description' => 'Seed confession branch under Religion 1.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * @return array{0:int,1:int,2:int}
     */
    private function upsertReminderTypes(\Illuminate\Support\Carbon $now): array
    {
        $holidayId = $this->upsertReminderType('holiday', 'Holiday', 'Holiday calendar reminders.', true, $now);
        $ritualId = $this->upsertReminderType('ritual', 'Ritual', 'Ritual timing reminders.', true, $now);
        $customId = $this->upsertReminderType('custom', 'Custom', 'Custom reminders such as weekly chapter.', true, $now);

        return [$holidayId, $ritualId, $customId];
    }

    private function upsertReminderType(
        string $typeKey,
        string $name,
        string $description,
        bool $isBuiltin,
        \Illuminate\Support\Carbon $now
    ): int {
        $id = DB::table('reminder_types')->where('type_key', $typeKey)->value('id');

        if ($id !== null) {
            DB::table('reminder_types')->where('id', $id)->update([
                'name' => $name,
                'description' => $description,
                'is_builtin' => $isBuiltin,
                'updated_at' => $now,
            ]);

            return (int) $id;
        }

        return (int) DB::table('reminder_types')->insertGetId([
            'type_key' => $typeKey,
            'name' => $name,
            'description' => $description,
            'is_builtin' => $isBuiltin,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * @return array<string, int>
     */
    private function upsertImplementations(
        int $holidayTypeId,
        int $ritualTypeId,
        int $customTypeId,
        \Illuminate\Support\Carbon $now
    ): array {
        return [
            'holiday_manual' => $this->upsertImplementation(
                $holidayTypeId,
                'holiday-manual',
                'manual',
                null,
                ['editor' => 'plain|markdown|wysiwyg', 'notes' => 'Manually curated holiday notes'],
                $now
            ),
            'holiday_auto' => $this->upsertImplementation(
                $holidayTypeId,
                'holiday-auto',
                'auto',
                'App\\Modules\\Reminders\\Services\\HolidayCalendarResolver',
                ['source' => 'calendar-provider', 'tz' => 'UTC'],
                $now
            ),
            'ritual_manual' => $this->upsertImplementation(
                $ritualTypeId,
                'ritual-manual',
                'manual',
                null,
                ['editor' => 'plain|markdown|wysiwyg', 'notes' => 'Manual ritual timing + text'],
                $now
            ),
            'ritual_auto' => $this->upsertImplementation(
                $ritualTypeId,
                'ritual-auto',
                'auto',
                'App\\Modules\\Reminders\\Services\\RitualScheduleResolver',
                ['source' => 'ritual-engine'],
                $now
            ),
            'custom_manual' => $this->upsertImplementation(
                $customTypeId,
                'custom-manual',
                'manual',
                null,
                ['editor' => 'plain|markdown|wysiwyg', 'notes' => 'Manual custom reminder'],
                $now
            ),
            'custom_auto' => $this->upsertImplementation(
                $customTypeId,
                'custom-auto',
                'auto',
                'App\\Modules\\Reminders\\Services\\WeeklyChapterResolver',
                ['source' => 'chapter-provider', 'cadence' => 'weekly'],
                $now
            ),
        ];
    }

    private function upsertImplementation(
        int $reminderTypeId,
        string $implementationKey,
        string $mode,
        ?string $handlerClass,
        array $config,
        \Illuminate\Support\Carbon $now
    ): int {
        $id = DB::table('reminder_implementations')
            ->where('reminder_type_id', $reminderTypeId)
            ->where('implementation_key', $implementationKey)
            ->value('id');

        if ($id !== null) {
            DB::table('reminder_implementations')->where('id', $id)->update([
                'implementation_mode' => $mode,
                'handler_class' => $handlerClass,
                'config_json' => json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'updated_at' => $now,
            ]);

            return (int) $id;
        }

        return (int) DB::table('reminder_implementations')->insertGetId([
            'reminder_type_id' => $reminderTypeId,
            'implementation_key' => $implementationKey,
            'implementation_mode' => $mode,
            'handler_class' => $handlerClass,
            'config_json' => json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * @return array<string, int>
     */
    private function upsertRituals(int $confessionId, \Illuminate\Support\Carbon $now): array
    {
        return [
            'ritual-1' => $this->upsertRitual($confessionId, 'ritual-1', 'Ritual 1', 'Ritual definition one.', $now),
            'ritual-2' => $this->upsertRitual($confessionId, 'ritual-2', 'Ritual 2', 'Ritual definition two.', $now),
            'ritual-3' => $this->upsertRitual($confessionId, 'ritual-3', 'Ritual 3', 'Ritual definition three.', $now),
        ];
    }

    private function upsertRitual(
        int $confessionId,
        string $ritualKey,
        string $name,
        string $description,
        \Illuminate\Support\Carbon $now
    ): int {
        $id = DB::table('rituals')
            ->where('confession_id', $confessionId)
            ->where('ritual_key', $ritualKey)
            ->value('id');

        if ($id !== null) {
            DB::table('rituals')->where('id', $id)->update([
                'name' => $name,
                'description' => $description,
                'is_active' => true,
                'updated_at' => $now,
            ]);

            return (int) $id;
        }

        return (int) DB::table('rituals')->insertGetId([
            'confession_id' => $confessionId,
            'ritual_key' => $ritualKey,
            'name' => $name,
            'description' => $description,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * @return array<string, int>
     */
    private function upsertCommands(int $confessionId, \Illuminate\Support\Carbon $now): array
    {
        return [
            'command-1' => $this->upsertCommand(
                confessionId: $confessionId,
                commandKey: 'command-1',
                trigger: '/command1',
                name: 'Command 1',
                description: 'Manual trigger for reminder command 1.',
                now: $now
            ),
            'command-2' => $this->upsertCommand(
                confessionId: $confessionId,
                commandKey: 'command-2',
                trigger: '/command2',
                name: 'Command 2',
                description: 'Manual trigger for reminder command 2.',
                now: $now
            ),
        ];
    }

    private function upsertCommand(
        int $confessionId,
        string $commandKey,
        string $trigger,
        string $name,
        string $description,
        \Illuminate\Support\Carbon $now
    ): int {
        $id = DB::table('religion_commands')
            ->where('confession_id', $confessionId)
            ->where('command_key', $commandKey)
            ->value('id');

        if ($id !== null) {
            DB::table('religion_commands')->where('id', $id)->update([
                'trigger' => $trigger,
                'name' => $name,
                'description' => $description,
                'is_active' => true,
                'updated_at' => $now,
            ]);

            return (int) $id;
        }

        return (int) DB::table('religion_commands')->insertGetId([
            'confession_id' => $confessionId,
            'command_key' => $commandKey,
            'trigger' => $trigger,
            'name' => $name,
            'description' => $description,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * @param  array<string, int>  $rituals
     * @param  array<string, int>  $commands
     * @param  array<string, int>  $implementations
     */
    private function upsertReminders(
        int $confessionId,
        array $rituals,
        array $commands,
        int $holidayTypeId,
        int $customTypeId,
        array $implementations,
        \Illuminate\Support\Carbon $now
    ): array {
        $reminder1Id = $this->upsertReminder(
            confessionId: $confessionId,
            title: 'Reminder 1',
            ritualId: $rituals['ritual-1'] ?? null,
            reminderTypeId: $holidayTypeId,
            implementationId: $implementations['holiday_manual'] ?? null,
            commandId: $commands['command-1'] ?? null,
            contentText: "## Holiday Reminder\n\nThis is **markdown** text for holiday reminder 1.",
            textFormat: 'markdown',
            frequencyMode: 'cron',
            frequencyValue: '0 9 * * 5',
            commandTrigger: '/command1',
            meta: ['channel' => 'group', 'notes' => 'Friday holiday prep'],
            now: $now
        );

        $reminder2Id = $this->upsertReminder(
            confessionId: $confessionId,
            title: 'Reminder 2',
            ritualId: $rituals['ritual-2'] ?? null,
            reminderTypeId: $customTypeId,
            implementationId: $implementations['custom_auto'] ?? null,
            commandId: $commands['command-2'] ?? null,
            contentText: '<p><strong>Weekly Chapter</strong> message from WYSIWYG editor.</p>',
            textFormat: 'wysiwyg',
            frequencyMode: 'command',
            frequencyValue: null,
            commandTrigger: '/command2',
            meta: ['channel' => 'dm', 'source' => 'weekly-chapter'],
            now: $now
        );

        return [
            'reminder-1' => $reminder1Id,
            'reminder-2' => $reminder2Id,
        ];
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function upsertReminder(
        int $confessionId,
        string $title,
        ?int $ritualId,
        int $reminderTypeId,
        ?int $implementationId,
        ?int $commandId,
        ?string $contentText,
        string $textFormat,
        string $frequencyMode,
        ?string $frequencyValue,
        ?string $commandTrigger,
        array $meta,
        \Illuminate\Support\Carbon $now
    ): int {
        $query = DB::table('religion_reminders')
            ->where('confession_id', $confessionId)
            ->where('title', $title);

        $id = $query->value('id');

        $payload = [
            'confession_id' => $confessionId,
            'ritual_id' => $ritualId,
            'reminder_type_id' => $reminderTypeId,
            'implementation_id' => $implementationId,
            'command_id' => $commandId,
            'title' => $title,
            'content_text' => $contentText,
            'text_format' => $textFormat,
            'frequency_mode' => $frequencyMode,
            'frequency_value' => $frequencyValue,
            'command_trigger' => $commandTrigger,
            'meta_json' => json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'is_active' => true,
            'updated_at' => $now,
        ];

        if ($id !== null) {
            DB::table('religion_reminders')->where('id', $id)->update($payload);

            return (int) $id;
        }

        $payload['created_at'] = $now;

        return (int) DB::table('religion_reminders')->insertGetId($payload);
    }

    /**
     * @param  array<string, int>  $reminders
     */
    private function seedRuntimeBindings(
        int $confessionId,
        array $reminders,
        \Illuminate\Support\Carbon $now
    ): void {
        [$botId, $groupLinkId] = $this->upsertSeedBotAndGroup($now);
        $messengerUserId = $this->upsertMessengerUser($now);

        $this->upsertUserGroupPreference(
            messengerUserId: $messengerUserId,
            messengerGroupLinkId: $groupLinkId,
            confessionId: $confessionId,
            now: $now
        );

        $target1Id = $this->upsertReminderDeliveryTarget(
            religionReminderId: $reminders['reminder-1'] ?? 0,
            targetType: 'group',
            messengerGroupLinkId: $groupLinkId,
            messengerUserId: null,
            channelKey: 'group:primary',
            now: $now
        );

        $target2Id = $this->upsertReminderDeliveryTarget(
            religionReminderId: $reminders['reminder-2'] ?? 0,
            targetType: 'dm',
            messengerGroupLinkId: null,
            messengerUserId: $messengerUserId,
            channelKey: 'dm:direct',
            now: $now
        );

        $this->upsertReminderRun(
            reminderId: $reminders['reminder-1'] ?? 0,
            targetId: $target1Id,
            key: 'seed-reminder-1-pending',
            status: 'pending',
            scheduledFor: $now->copy()->addHour(),
            sentAt: null,
            now: $now
        );

        $this->upsertReminderRun(
            reminderId: $reminders['reminder-2'] ?? 0,
            targetId: $target2Id,
            key: 'seed-reminder-2-sent',
            status: 'sent',
            scheduledFor: $now->copy()->subHour(),
            sentAt: $now->copy()->subMinutes(50),
            now: $now
        );
    }

    /**
     * @return array{0:int,1:int}
     */
    private function upsertSeedBotAndGroup(\Illuminate\Support\Carbon $now): array
    {
        $botId = DB::table('messenger_bots')
            ->where('driver', 'telegram')
            ->where('external_bot_id', 'seed-telegram-bot-1')
            ->value('id');

        if ($botId !== null) {
            DB::table('messenger_bots')->where('id', $botId)->update([
                'name' => 'Seed Telegram Bot',
                'bot_token' => 'seed-token-not-for-production',
                'username' => 'seed_rebe_bot',
                'is_active' => true,
                'updated_at' => $now,
            ]);
        } else {
            $botId = (int) DB::table('messenger_bots')->insertGetId([
                'name' => 'Seed Telegram Bot',
                'driver' => 'telegram',
                'bot_token' => 'seed-token-not-for-production',
                'external_bot_id' => 'seed-telegram-bot-1',
                'username' => 'seed_rebe_bot',
                'is_active' => true,
                'meta_json' => json_encode(['source' => 'seed'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $groupId = DB::table('messenger_group_links')
            ->where('messenger_bot_id', (int) $botId)
            ->where('external_chat_id', '-100123450001')
            ->value('id');

        if ($groupId !== null) {
            DB::table('messenger_group_links')->where('id', $groupId)->update([
                'title' => 'Seed Study Group',
                'is_active' => true,
                'updated_at' => $now,
            ]);
        } else {
            $groupId = (int) DB::table('messenger_group_links')->insertGetId([
                'messenger_bot_id' => (int) $botId,
                'external_chat_id' => '-100123450001',
                'title' => 'Seed Study Group',
                'is_active' => true,
                'meta_json' => json_encode(['source' => 'seed'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return [(int) $botId, (int) $groupId];
    }

    private function upsertMessengerUser(\Illuminate\Support\Carbon $now): int
    {
        $id = DB::table('messenger_users')
            ->where('driver', 'telegram')
            ->where('external_user_id', 'seed-telegram-user-1')
            ->value('id');

        if ($id !== null) {
            DB::table('messenger_users')->where('id', $id)->update([
                'username' => 'seed_user_1',
                'display_name' => 'Seed Telegram User 1',
                'language_code' => 'en',
                'is_active' => true,
                'updated_at' => $now,
            ]);

            return (int) $id;
        }

        return (int) DB::table('messenger_users')->insertGetId([
            'driver' => 'telegram',
            'external_user_id' => 'seed-telegram-user-1',
            'username' => 'seed_user_1',
            'display_name' => 'Seed Telegram User 1',
            'language_code' => 'en',
            'is_active' => true,
            'meta_json' => json_encode(['source' => 'seed'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function upsertUserGroupPreference(
        int $messengerUserId,
        int $messengerGroupLinkId,
        int $confessionId,
        \Illuminate\Support\Carbon $now
    ): void {
        $id = DB::table('user_group_religion_preferences')
            ->where('messenger_user_id', $messengerUserId)
            ->where('messenger_group_link_id', $messengerGroupLinkId)
            ->value('id');

        $payload = [
            'messenger_user_id' => $messengerUserId,
            'messenger_group_link_id' => $messengerGroupLinkId,
            'confession_id' => $confessionId,
            'language_pack_id' => null,
            'ritual_opt_in' => true,
            'is_active' => true,
            'meta_json' => json_encode(['source' => 'seed'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => $now,
        ];

        if ($id !== null) {
            DB::table('user_group_religion_preferences')->where('id', $id)->update($payload);

            return;
        }

        $payload['created_at'] = $now;
        DB::table('user_group_religion_preferences')->insert($payload);
    }

    private function upsertReminderDeliveryTarget(
        int $religionReminderId,
        string $targetType,
        ?int $messengerGroupLinkId,
        ?int $messengerUserId,
        ?string $channelKey,
        \Illuminate\Support\Carbon $now
    ): int {
        $query = DB::table('reminder_delivery_targets')
            ->where('religion_reminder_id', $religionReminderId)
            ->where('target_type', $targetType)
            ->where('channel_key', $channelKey);

        $id = $query->value('id');

        $payload = [
            'religion_reminder_id' => $religionReminderId,
            'target_type' => $targetType,
            'messenger_group_link_id' => $messengerGroupLinkId,
            'messenger_user_id' => $messengerUserId,
            'channel_key' => $channelKey,
            'is_active' => true,
            'meta_json' => json_encode(['source' => 'seed'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => $now,
        ];

        if ($id !== null) {
            DB::table('reminder_delivery_targets')->where('id', $id)->update($payload);

            return (int) $id;
        }

        $payload['created_at'] = $now;

        return (int) DB::table('reminder_delivery_targets')->insertGetId($payload);
    }

    private function upsertReminderRun(
        int $reminderId,
        ?int $targetId,
        string $key,
        string $status,
        \Illuminate\Support\Carbon $scheduledFor,
        ?\Illuminate\Support\Carbon $sentAt,
        \Illuminate\Support\Carbon $now
    ): void {
        $id = DB::table('reminder_runs')
            ->where('idempotency_key', $key)
            ->value('id');

        $payload = [
            'religion_reminder_id' => $reminderId,
            'reminder_delivery_target_id' => $targetId,
            'next_run_at_utc' => $status === 'pending' ? $scheduledFor : null,
            'scheduled_for_utc' => $scheduledFor,
            'sent_at' => $sentAt,
            'status' => $status,
            'idempotency_key' => $key,
            'attempt_count' => $status === 'sent' ? 1 : 0,
            'last_error' => null,
            'payload_json' => json_encode(['source' => 'seed'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => $now,
        ];

        if ($id !== null) {
            DB::table('reminder_runs')->where('id', $id)->update($payload);

            return;
        }

        $payload['created_at'] = $now;
        DB::table('reminder_runs')->insert($payload);
    }
}
