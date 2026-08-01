<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Services;

use App\Modules\Messenger\Models\MessengerGroupLink;
use App\Modules\Messenger\Models\MessengerUser;
use App\Modules\Religions\Models\Confession;
use App\Modules\Religions\Models\Religion;
use App\Modules\Religions\Models\ReligionCommand;
use App\Modules\Religions\Models\Ritual;
use App\Modules\Religions\Models\UserGroupReligionPreference;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class TelegramGroupReligionInteractionService
{
    /**
     * @param  array<int, string>  $args
     */
    public function handleBuiltInCommand(
        string $trigger,
        array $args,
        MessengerUser $messengerUser,
        ?MessengerGroupLink $groupLink
    ): ?string {
        if ($this->isAnonymousTelegramProxyUser($messengerUser) && in_array($trigger, ['/religion', '/confession', '/ritual'], true)) {
            return 'Telegram sent this command as GroupAnonymousBot. Turn off anonymous admin mode and send the command again from your real account.';
        }

        if ($trigger === '/ritual') {
            $ritualAction = strtolower((string) ($args[0] ?? ''));
            if ($ritualAction !== '' && $ritualAction !== 'list') {
                return null;
            }
        }

        if (! in_array($trigger, ['/help', '/commands', '/aboutme', '/whatnext', '/follower', '/religion', '/confession', '/ritual'], true)) {
            return null;
        }

        return match ($trigger) {
            '/help', '/commands' => $this->handleHelpCommand($messengerUser, $groupLink),
            '/aboutme' => $this->handleAboutMeCommand(),
            '/whatnext' => $this->handleWhatNextCommand($args),
            '/follower' => $this->handleFollowerCommand($args, $groupLink),
            '/religion' => $this->handleReligionCommand($args, $messengerUser, $groupLink),
            '/confession' => $this->handleConfessionCommand($args, $messengerUser, $groupLink),
            '/ritual' => $this->handleRitualCommand($args, $messengerUser, $groupLink),
            default => null,
        };
    }

    private function handleHelpCommand(MessengerUser $messengerUser, ?MessengerGroupLink $groupLink): string
    {
        $lines = [
            'Available commands:',
            '- /help',
            '- /aboutme',
            '- /whatNext_ru',
            '- /whatNext_eng',
            '- /follower',
            '- /religionList',
            '- /religionChoose_<religion-token>',
            '- /religionChoose_<religion-token>__<confession-token>',
            '- /religionDescribe_<religion-token>',
            '- /confessionDescribe_<confession-token>',
            '- /confessionLeave_<confession-token>',
            '- /ritualList',
            '',
            'Tip: command format is /camelCaseCommand_arg1__arg2',
            'Tip: run /religionList to get generated commands with real tokens for active religions/confessions.',
            'Tip: you can join multiple religions/confessions in the same group.',
        ];

        $religions = Religion::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'slug']);

        if ($religions->isNotEmpty()) {
            $confessions = Confession::query()
                ->whereIn('religion_id', $religions->pluck('id')->all())
                ->where('is_active', true)
                ->orderBy('id')
                ->get(['religion_id', 'slug'])
                ->groupBy('religion_id');

            $lines[] = '';
            $lines[] = 'Shortcut examples (generated from current data):';
            $lines[] = '- /religionList';

            foreach ($religions as $religion) {
                $religionToken = $this->toCommandToken((string) $religion->slug);
                if ($religionToken === '') {
                    continue;
                }

                $lines[] = '- /religionChoose_'.$religionToken;
                $lines[] = '- /religionDescribe_'.$religionToken;

                $religionConfessions = $confessions->get((int) $religion->id);
                if ($religionConfessions === null) {
                    continue;
                }

                foreach ($religionConfessions as $confession) {
                    $confessionSlug = (string) ($confession->slug ?? '');
                    if ($confessionSlug === '') {
                        continue;
                    }

                    $confessionToken = $this->toCommandToken($confessionSlug);
                    if ($confessionToken === '') {
                        continue;
                    }

                    $lines[] = '- /religionChoose_'.$religionToken.'__'.$confessionToken;
                    $lines[] = '- /confessionDescribe_'.$confessionToken;
                    $lines[] = '- /confessionLeave_'.$confessionToken;
                }
            }
        }

        $globalMappedCommands = ReligionCommand::query()
            ->where('is_active', true)
            ->orderBy('trigger')
            ->pluck('trigger')
            ->filter(static fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $trigger): string => $this->toMappedCamelCommand($trigger))
            ->filter(static fn (string $trigger): bool => $trigger !== '')
            ->unique()
            ->values();

        if ($globalMappedCommands->isNotEmpty()) {
            $lines[] = '';
            $lines[] = 'Mapped commands (active):';

            foreach ($globalMappedCommands as $mappedCommand) {
                $lines[] = '- '.$mappedCommand;
            }
        }

        if ($groupLink === null) {
            $lines[] = '';
            $lines[] = 'Tip: run this inside a linked group to see group-specific commands.';

            return implode("\n", $lines);
        }

        $preferences = $this->getActivePreferences($messengerUser, $groupLink);

        if ($preferences->isEmpty()) {
            $lines[] = '';
            $lines[] = 'No confessions selected in this group yet. Use /religionChoose_<religion-token>__<confession-token>.';

            return implode("\n", $lines);
        }

        $confessionIds = $preferences->pluck('confession_id')->map(static fn (mixed $id): int => (int) $id)->all();

        $confessions = Confession::query()
            ->whereIn('id', $confessionIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'religion_id']);

        if ($confessions->isEmpty()) {
            $lines[] = '';
            $lines[] = 'Your selected confessions are missing. Re-select using /religion choose <religion-slug> [confession-slug].';

            return implode("\n", $lines);
        }

        $lines[] = '';
        $selectedReligionIds = $confessions->pluck('religion_id')->map(static fn (mixed $id): int => (int) $id)->all();
        $religions = Religion::query()
            ->whereIn('id', $selectedReligionIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['name', 'slug']);

        if ($religions->isNotEmpty()) {
            $lines[] = 'Selected religions in this group:';
            foreach ($religions as $religion) {
                $lines[] = "- {$religion->name} ({$religion->slug})";
            }

            $lines[] = '';
        }

        $lines[] = 'Selected confessions in this group:';

        foreach ($confessions as $confession) {
            $lines[] = "- {$confession->name} ({$confession->slug})";
        }

        $triggers = ReligionCommand::query()
            ->whereIn('confession_id', $confessions->pluck('id')->all())
            ->where('is_active', true)
            ->orderBy('trigger')
            ->pluck('trigger')
            ->filter(static fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $trigger): string => $this->toMappedCamelCommand($trigger))
            ->filter(static fn (string $trigger): bool => $trigger !== '')
            ->unique()
            ->values();

        if ($triggers->isEmpty()) {
            $lines[] = 'No mapped command triggers found for your selected confessions yet.';
            $lines[] = 'Create them in admin: /admin/religions/commands/link';

            return implode("\n", $lines);
        }

        $lines[] = 'Mapped triggers for your selected confessions:';

        foreach ($triggers as $triggerValue) {
            $lines[] = "- {$triggerValue}";
        }

        return implode("\n", $lines);
    }

    private function handleAboutMeCommand(): string
    {
        return 'AboutMe markdown files are attached.';
    }

    /**
     * @param  array<int, string>  $args
     */
    private function handleWhatNextCommand(array $args): string
    {
        $lang = strtolower(trim((string) ($args[0] ?? 'eng')));

        return in_array($lang, ['ru', 'rus'], true)
            ? $this->whatNextRuText()
            : $this->whatNextEngText();
    }

    private function whatNextRuText(): string
    {
        return implode("\n", [
            'Зачем нужен бот:',
            'Бот нужен, чтобы в группе было проще блюсти культуру выбранной веры: помнить ритуалы, слова, привычки, напоминания и общий контекст конфессии.',
            '',
            'Что может делать пользователь:',
            '1. Выбрать религию и конфессию через /religionList и /religionChoose_<religion>__<confession>.',
            '2. Смотреть свой контекст через /help.',
            '3. Смотреть участников по конфессиям через /follower.',
            '4. Смотреть ритуалы через /ritualList.',
            '5. Получать word of the day и другие напоминания для выбранной конфессии.',
            '',
            'Что может делать админ:',
            '1. Подключить бота и группу через /bots/setup.',
            '2. Настраивать религии, конфессии, ритуалы, language packs и reminders в /admin/religions.',
            '3. Настраивать команды, импорт/экспорт и smoke tests в /admin/religions/commands/link.',
            '4. Делать кастомные религии, кастомные конфессии, кастомные ритуалы, собственные слова и собственные reminder flows.',
            '',
            'Простой пример для иудаизма:',
            'Админ может собрать Judaism pack: конфессию, Hebrew words, ритуалы и daily word reminder. Пользователь выбирает эту конфессию и получает напоминания и слова, которые помогают держать культуру этой веры в живом использовании.',
            '',
            'Полезные команды:',
            '- /help',
            '- /aboutme',
            '- /whatNext_eng',
            '- /religionList',
            '- /follower',
        ]);
    }

    private function whatNextEngText(): string
    {
        return implode("\n", [
            'Why this bot exists:',
            'This bot helps a group preserve the culture of a chosen faith: rituals, words, reminders, and shared confession context inside daily chat usage.',
            '',
            'What a user can do:',
            '1. Choose a religion and confession with /religionList and /religionChoose_<religion>__<confession>.',
            '2. Check current context with /help.',
            '3. List followers by confession with /follower.',
            '4. List rituals with /ritualList.',
            '5. Receive word-of-the-day and other reminders for the selected confession.',
            '',
            'What an admin can do:',
            '1. Connect the bot and group through /bots/setup.',
            '2. Manage religions, confessions, rituals, language packs, and reminders in /admin/religions.',
            '3. Manage command mappings, import/export, and smoke tests in /admin/religions/commands/link.',
            '4. Create custom religions, custom confessions, custom rituals, custom word packs, and custom reminder flows.',
            '',
            'Simple Judaism example:',
            'An admin can assemble a Judaism pack with a confession, Hebrew words, rituals, and a daily word reminder. A user chooses that confession and then receives words and reminders that help keep the culture of that faith active in the group.',
            '',
            'Useful commands:',
            '- /help',
            '- /aboutme',
            '- /whatNext_ru',
            '- /religionList',
            '- /follower',
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function aboutMeAttachmentPaths(): array
    {
        return array_values(array_filter([
            $this->readAboutMeAttachmentPath('README.md'),
            $this->readAboutMeAttachmentPath('STARTUP.md'),
            $this->readAboutMeAttachmentPath('MANUAL_TESTING.md'),
            $this->readAboutMeAttachmentPath('IMPLEMENTED.md'),
            $this->readAboutMeAttachmentPath('ABOUTME_IMPORT_PATTERN.md'),
            $this->readAboutMeAttachmentPath('docs/09-judaism-pack-todo.md'),
        ]));
    }

    private function readAboutMeAttachmentPath(string $relativePath): ?string
    {
        $absolutePath = base_path($relativePath);

        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return null;
        }

        return $absolutePath;
    }

    /**
     * @param  array<int, string>  $args
     */
    private function handleFollowerCommand(array $args, ?MessengerGroupLink $groupLink): string
    {
        if ($groupLink === null) {
            return 'This command requires a linked group context.';
        }

        $requestedConfessionSlug = strtolower(trim((string) ($args[0] ?? '')));

        $query = UserGroupReligionPreference::query()
            ->join('messenger_users as u', 'u.id', '=', 'user_group_religion_preferences.messenger_user_id')
            ->join('confessions as c', 'c.id', '=', 'user_group_religion_preferences.confession_id')
            ->join('religions as r', 'r.id', '=', 'c.religion_id')
            ->where('user_group_religion_preferences.messenger_group_link_id', $groupLink->id)
            ->where('user_group_religion_preferences.is_active', true)
            ->where('c.is_active', true)
            ->where('r.is_active', true);

        if ($requestedConfessionSlug !== '') {
            $query->where('c.slug', $requestedConfessionSlug);
        }

        $rows = $query
            ->orderBy('r.name')
            ->orderBy('c.name')
            ->orderBy('u.username')
            ->orderBy('u.display_name')
            ->get([
                'r.name as religion_name',
                'r.slug as religion_slug',
                'c.name as confession_name',
                'c.slug as confession_slug',
                'u.username',
                'u.display_name',
                'u.external_user_id',
                'u.meta_json',
            ]);

        if ($rows->isEmpty()) {
            return $requestedConfessionSlug !== ''
                ? "No active followers found for confession '{$requestedConfessionSlug}' in this group."
                : 'No active followers with confession selections found in this group.';
        }

        $grouped = [];

        foreach ($rows as $row) {
            if ($this->shouldSkipFollowerRow($row)) {
                continue;
            }

            $confessionLabel = trim((string) ($row->confession_name ?? 'Unknown confession'));
            $confessionSlug = trim((string) ($row->confession_slug ?? ''));
            $religionLabel = trim((string) ($row->religion_name ?? 'Unknown religion'));
            $religionSlug = trim((string) ($row->religion_slug ?? ''));
            $key = $confessionLabel.'|'.$confessionSlug.'|'.$religionLabel.'|'.$religionSlug;

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'header' => 'Religion: '
                        .$religionLabel
                        .($religionSlug !== '' ? ' ('.$religionSlug.')' : '')
                        ."\nConfession: "
                        .$confessionLabel
                        .($confessionSlug !== '' ? ' ('.$confessionSlug.')' : ''),
                    'followers' => [],
                ];
            }

            $grouped[$key]['followers'][] = $this->formatFollowerLabel(
                username: (string) ($row->username ?? ''),
                displayName: (string) ($row->display_name ?? ''),
                externalUserId: (string) ($row->external_user_id ?? '')
            );
        }

        if ($grouped === []) {
            return $requestedConfessionSlug !== ''
                ? "No active human followers found for confession '{$requestedConfessionSlug}' in this group."
                : 'No active human followers with confession selections found in this group.';
        }

        $lines = ['Followers in this group:'];

        foreach ($grouped as $entry) {
            $lines[] = '';
            $lines[] = $entry['header'];

            foreach (array_values(array_unique($entry['followers'])) as $follower) {
                $lines[] = '- '.$follower;
            }
        }

        return implode("\n", $lines);
    }

    private function formatFollowerLabel(string $username, string $displayName, string $externalUserId): string
    {
        $username = trim($username);
        if ($username !== '') {
            return '@'.$username;
        }

        $displayName = trim($displayName);
        if ($displayName !== '') {
            return $displayName;
        }

        $externalUserId = trim($externalUserId);

        return $externalUserId !== '' ? 'user:'.$externalUserId : 'user';
    }

    private function isAnonymousTelegramProxyUser(MessengerUser $messengerUser): bool
    {
        $username = strtolower(trim((string) ($messengerUser->username ?? '')));
        if ($username === 'groupanonymousbot') {
            return true;
        }

        $externalUserId = trim((string) ($messengerUser->external_user_id ?? ''));
        if ($externalUserId === '1087968824') {
            return true;
        }

        $meta = $messengerUser->meta_json;

        if (is_string($meta) && $meta !== '') {
            $decoded = json_decode($meta, true);
            $meta = is_array($decoded) ? $decoded : null;
        }

        return is_array($meta)
            && (bool) ($meta['is_bot'] ?? false) === true
            && $username === 'groupanonymousbot';
    }

    private function shouldSkipFollowerRow(object $row): bool
    {
        $username = strtolower(trim((string) ($row->username ?? '')));
        if ($username === 'groupanonymousbot') {
            return true;
        }

        $meta = $row->meta_json ?? null;

        if (is_string($meta) && $meta !== '') {
            $decoded = json_decode($meta, true);
            $meta = is_array($decoded) ? $decoded : null;
        }

        return is_array($meta) && (bool) ($meta['is_bot'] ?? false) === true;
    }

    /**
     * @param  array<int, string>  $args
     */
    private function handleReligionCommand(array $args, MessengerUser $messengerUser, ?MessengerGroupLink $groupLink): string
    {
        $action = strtolower((string) ($args[0] ?? 'list'));

        if ($action === 'list') {
            $religions = Religion::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'description']);

            if ($religions->isEmpty()) {
                return 'No active religions found.';
            }

            $confessions = Confession::query()
                ->whereIn('religion_id', $religions->pluck('id')->all())
                ->where('is_active', true)
                ->orderBy('id')
                ->get(['religion_id', 'slug'])
                ->groupBy('religion_id');

            $lines = ['Available religions:'];
            foreach ($religions as $religion) {
                $lines[] = "- {$religion->name} ({$religion->slug})";
            }

            $lines[] = '';
            $lines[] = 'Quick command links:';
            $lines[] = '- /religionList';

            foreach ($religions as $religion) {
                $religionToken = $this->toCommandToken((string) $religion->slug);
                $lines[] = '- /religionChoose_'.$religionToken;
                $lines[] = '- /religionDescribe_'.$religionToken;

                $religionConfessions = $confessions->get((int) $religion->id);
                if ($religionConfessions === null) {
                    continue;
                }

                foreach ($religionConfessions as $confession) {
                    $confessionSlug = (string) ($confession->slug ?? '');
                    if ($confessionSlug === '') {
                        continue;
                    }

                    $confessionToken = $this->toCommandToken($confessionSlug);
                    if ($confessionToken === '') {
                        continue;
                    }

                    $lines[] = '- /religionChoose_'.$religionToken.'__'.$confessionToken;
                }
            }

            return implode("\n", $lines);
        }

        if ($action === 'describe') {
            $religionSlug = strtolower((string) ($args[1] ?? ''));
            if ($religionSlug === '') {
                return 'Usage: /religion describe <religion-slug>';
            }

            $religion = Religion::query()
                ->where('slug', $religionSlug)
                ->where('is_active', true)
                ->first();

            if ($religion === null) {
                return "Religion '{$religionSlug}' not found.";
            }

            $confessionsCount = Confession::query()
                ->where('religion_id', $religion->id)
                ->where('is_active', true)
                ->count();

            $description = trim((string) ($religion->description ?? 'No description yet.'));

            return "Religion: {$religion->name} ({$religion->slug})\n"
                ."Confessions: {$confessionsCount}\n\n"
                ."{$description}";
        }

        if ($action === 'choose') {
            if ($groupLink === null) {
                return 'This command requires a linked group context.';
            }

            $religionSlug = strtolower((string) ($args[1] ?? ''));
            if ($religionSlug === '') {
                return 'Usage: /religion choose <religion-slug> [confession-slug]';
            }

            $religion = Religion::query()
                ->where('slug', $religionSlug)
                ->where('is_active', true)
                ->first();

            if ($religion === null) {
                return "Religion '{$religionSlug}' not found.";
            }

            $requestedConfessionSlug = strtolower((string) ($args[2] ?? ''));

            $confessionQuery = Confession::query()
                ->where('religion_id', $religion->id)
                ->where('is_active', true);

            if ($requestedConfessionSlug !== '') {
                $confessionQuery->where('slug', $requestedConfessionSlug);
            }

            $confession = $confessionQuery->orderBy('id')->first();

            if ($confession === null) {
                if ($requestedConfessionSlug !== '') {
                    return "Confession '{$requestedConfessionSlug}' not found under {$religion->name}.";
                }

                return "Religion '{$religion->name}' has no active confessions yet.";
            }

            UserGroupReligionPreference::query()->updateOrCreate(
                [
                    'messenger_user_id' => $messengerUser->id,
                    'messenger_group_link_id' => $groupLink->id,
                    'confession_id' => $confession->id,
                ],
                [
                    'language_pack_id' => null,
                    'ritual_opt_in' => true,
                    'is_active' => true,
                ]
            );

            $religionDescription = trim((string) ($religion->description ?? 'No description yet.'));
            $confessionDescription = trim((string) ($confession->description ?? 'No description yet.'));
            $welcomeMessage = trim((string) ($confession->welcome_message ?? ''));

            if ($welcomeMessage === '') {
                $welcomeMessage = 'Welcome';
            }

            return "{$welcomeMessage}\n"
                ."Religion: {$religion->name}\n"
                ."Joined confession: {$confession->name} ({$confession->slug})\n\n"
                ."Religion description:\n{$religionDescription}\n\n"
                ."Confession description:\n{$confessionDescription}\n\n"
                .'Use /ritual list to see rituals.';
        }

        return 'Usage: /religion list | /religion describe <religion-slug> | /religion choose <religion-slug> [confession-slug]';
    }

    /**
     * @param  array<int, string>  $args
     */
    private function handleConfessionCommand(array $args, MessengerUser $messengerUser, ?MessengerGroupLink $groupLink): string
    {
        $action = strtolower((string) ($args[0] ?? 'describe'));

        if ($action === 'leave') {
            if ($groupLink === null) {
                return 'This command requires a linked group context.';
            }

            $slug = strtolower((string) ($args[1] ?? ''));
            if ($slug === '') {
                return 'Usage: /confession leave <confession-slug>';
            }

            $confession = Confession::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->first();

            if ($confession === null) {
                return "Confession '{$slug}' not found.";
            }

            $updated = UserGroupReligionPreference::query()
                ->where('messenger_user_id', $messengerUser->id)
                ->where('messenger_group_link_id', $groupLink->id)
                ->where('confession_id', $confession->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            if ($updated < 1) {
                return "You are not currently joined to confession '{$confession->slug}' in this group.";
            }

            return "Left confession: {$confession->name} ({$confession->slug}).";
        }

        if ($action !== 'describe') {
            return 'Usage: /confessionDescribe_<confession-token> | /confessionLeave_<confession-token>';
        }

        $slug = strtolower((string) ($args[1] ?? ''));
        if ($slug === '') {
            return 'Usage: /confession describe <confession-slug>';
        }

        $query = Confession::query()
            ->where('slug', $slug)
            ->where('is_active', true);

        if ($groupLink !== null) {
            $selectedReligionIds = Confession::query()
                ->whereIn('id', $this->getActivePreferences($messengerUser, $groupLink)->pluck('confession_id')->all())
                ->where('is_active', true)
                ->pluck('religion_id')
                ->map(static fn (mixed $id): int => (int) $id)
                ->filter(static fn (int $id): bool => $id > 0)
                ->unique()
                ->values()
                ->all();

            if ($selectedReligionIds !== []) {
                $query->whereIn('religion_id', $selectedReligionIds);
            }
        }

        $confession = $query->first();

        if ($confession === null) {
            return "Confession '{$slug}' not found.";
        }

        $religionName = (string) Religion::query()
            ->where('id', $confession->religion_id)
            ->value('name');

        $description = trim((string) ($confession->description ?? 'No description yet.'));

        return "Confession: {$confession->name} ({$confession->slug})\n"
            ."Religion: {$religionName}\n\n"
            ."{$description}";
    }

    /**
     * @param  array<int, string>  $args
     */
    private function handleRitualCommand(array $args, MessengerUser $messengerUser, ?MessengerGroupLink $groupLink): string
    {
        $action = strtolower((string) ($args[0] ?? 'list'));
        if ($action !== 'list') {
            return 'Usage: /ritual list';
        }

        if ($groupLink === null) {
            return 'This command requires a linked group context.';
        }

        $preferences = $this->getActivePreferences($messengerUser, $groupLink);

        if ($preferences->isEmpty()) {
            return 'Choose religion first: /religionChoose_<religion-token>__<confession-token>';
        }

        $confessionIds = $preferences->pluck('confession_id')->map(static fn (mixed $id): int => (int) $id)->all();

        $confessions = Confession::query()
            ->whereIn('id', $confessionIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        if ($confessions->isEmpty()) {
            return 'Selected confessions no longer exist. Please choose religion again.';
        }

        $rituals = Ritual::query()
            ->whereIn('confession_id', $confessions->pluck('id')->all())
            ->where('is_active', true)
            ->orderBy('confession_id')
            ->orderBy('id')
            ->get(['confession_id', 'ritual_key', 'name', 'description']);

        if ($rituals->isEmpty()) {
            return 'No rituals found for your selected confessions.';
        }

        $confessionById = $confessions->keyBy('id');
        $lines = ['Rituals for your selected confessions:'];

        foreach ($rituals as $ritual) {
            $confessionForRitual = $confessionById->get((int) $ritual->confession_id);
            $confessionName = is_object($confessionForRitual)
                ? (string) ($confessionForRitual->name ?? 'Unknown confession')
                : 'Unknown confession';
            $description = trim((string) ($ritual->description ?? ''));
            $description = $description !== '' ? $description : 'No description';

            $lines[] = "- {$confessionName}: {$ritual->name} ({$ritual->ritual_key})";
            $lines[] = "  {$description}";
            $lines[] = '';
        }

        return rtrim(implode("\n", $lines));
    }

    /**
     * @return EloquentCollection<int, UserGroupReligionPreference>
     */
    private function getActivePreferences(MessengerUser $messengerUser, MessengerGroupLink $groupLink): EloquentCollection
    {
        return UserGroupReligionPreference::query()
            ->where('messenger_user_id', $messengerUser->id)
            ->where('messenger_group_link_id', $groupLink->id)
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->get();
    }

    private function toCommandToken(string $value): string
    {
        $token = (string) preg_replace('/[^a-z0-9]+/i', '_', strtolower(trim($value)));

        return trim($token, '_');
    }

    private function toMappedCamelCommand(string $trigger): string
    {
        $parts = preg_split('/\s+/', trim(strtolower($trigger))) ?: [];
        if ($parts === []) {
            return '';
        }

        $normalized = [];

        foreach ($parts as $index => $part) {
            $cleanPart = $index === 0 ? ltrim($part, '/') : $part;
            $cleanPart = str_replace(['<', '>', '[', ']'], '', $cleanPart);
            $chunk = (string) preg_replace('/[^a-z0-9]+/i', ' ', trim($cleanPart));
            $words = array_values(array_filter(explode(' ', $chunk), static fn (string $word): bool => $word !== ''));

            foreach ($words as $word) {
                $normalized[] = $word;
            }
        }

        if ($normalized === []) {
            return '';
        }

        $camel = (string) array_shift($normalized);

        foreach ($normalized as $word) {
            $camel .= ucfirst($word);
        }

        return '/'.$camel;
    }
}
