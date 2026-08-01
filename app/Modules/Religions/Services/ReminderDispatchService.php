<?php

declare(strict_types=1);

namespace App\Modules\Religions\Services;

use App\Modules\Messenger\Models\MessengerGroupLink;
use App\Modules\Messenger\Services\MessengerClientRegistry;
use App\Modules\Religions\Models\LanguageWord;
use App\Modules\Religions\Models\ReligionReminder;
use App\Modules\Religions\Models\UserGroupReligionPreference;
use Cron\CronExpression;
use Illuminate\Support\Carbon;

class ReminderDispatchService
{
    public function __construct(
        private readonly MessengerClientRegistry $clientRegistry,
    ) {}

    /**
     * @return array{checked:int,dispatched:int,messages:int}
     */
    public function dispatchDue(?int $onlyReminderId = null, bool $force = false): array
    {
        $now = now('UTC');

        $query = ReligionReminder::query()
            ->with(['ritual', 'command', 'languagePack', 'confession.languagePack'])
            ->where('is_active', true)
            ->whereIn('frequency_mode', ['cron', 'interval']);

        if ($onlyReminderId !== null && $onlyReminderId > 0) {
            $query->where('id', $onlyReminderId);
        }

        $checked = 0;
        $dispatched = 0;
        $messages = 0;

        foreach ($query->orderBy('id')->get() as $reminder) {
            $checked++;

            $groupIds = UserGroupReligionPreference::query()
                ->where('confession_id', (int) $reminder->confession_id)
                ->where('is_active', true)
                ->pluck('messenger_group_link_id')
                ->map(static fn (mixed $id): int => (int) $id)
                ->filter(static fn (int $id): bool => $id > 0)
                ->unique()
                ->values()
                ->all();

            if ($groupIds === []) {
                continue;
            }

            $wordMode = (bool) ($reminder->word_mode ?? false);
            $meta = is_array($reminder->meta_json) ? $reminder->meta_json : [];

            if (! $force) {
                $isDue = $wordMode
                    ? $this->isWordModeDueForAnyGroup($reminder, $groupIds, $now, $meta)
                    : $this->isDue($reminder, $now);

                if (! $isDue) {
                    continue;
                }
            }

            $groups = MessengerGroupLink::query()
                ->with('bot')
                ->whereIn('id', $groupIds)
                ->where('is_active', true)
                ->get();

            if ($wordMode) {
                $text = $this->buildWordModeMessage($reminder);
                if ($text === null) {
                    continue;
                }
            } else {
                $text = trim((string) ($reminder->content_text ?? ''));
                if ($text === '') {
                    $text = trim((string) ($reminder->ritual?->description ?? ''));
                }
                if ($text === '') {
                    $text = trim((string) $reminder->title);
                }

                if ($text === '') {
                    $text = 'Reminder';
                }
            }

            $sentForReminder = 0;

            foreach ($groups as $group) {
                $bot = $group->bot;
                if ($bot === null || ! $bot->is_active) {
                    continue;
                }

                if ($wordMode && ! $this->shouldDispatchWordOfDayToGroup($reminder, (int) $group->id, $now, $meta)) {
                    continue;
                }

                $mentionPrefix = $this->buildGroupMentionPrefix((int) $group->id, (int) $reminder->confession_id);
                $message = $mentionPrefix !== '' ? ($mentionPrefix."\n\n".$text) : $text;

                try {
                    $this->clientRegistry
                        ->forDriver((string) $bot->driver)
                        ->sendMessage((string) $bot->bot_token, (string) $group->external_chat_id, $message);

                    if ($wordMode) {
                        $meta = $this->markWordOfDayDispatchedForGroup($reminder, (int) $group->id, $now, $meta);
                    }

                    $messages++;
                    $sentForReminder++;
                } catch (\Throwable) {
                    // Skip broken bot/group delivery and continue with remaining targets.
                    continue;
                }
            }

            if ($sentForReminder > 0) {
                $meta['last_dispatched_at'] = $now->toIso8601String();
                $reminder->meta_json = $meta;
                $reminder->save();
                $dispatched++;
            }
        }

        return [
            'checked' => $checked,
            'dispatched' => $dispatched,
            'messages' => $messages,
        ];
    }

    /**
     * @param  array<int, int>  $groupIds
     * @param  array<string, mixed>  $meta
     */
    private function isWordModeDueForAnyGroup(ReligionReminder $reminder, array $groupIds, Carbon $now, array $meta): bool
    {
        $targetDate = $this->resolveWordModeTargetDate($reminder, $now);

        if ($targetDate === null) {
            return false;
        }

        foreach ($groupIds as $groupId) {
            if ($this->isWordModePendingForGroupId($groupId, $targetDate, $meta)) {
                return true;
            }
        }

        return false;
    }

    private function resolveWordModeTargetDate(ReligionReminder $reminder, Carbon $now): ?string
    {
        $timezone = $this->resolveReminderTimezone($reminder);

        if ((string) $reminder->frequency_mode === 'cron') {
            $expression = trim((string) ($reminder->frequency_value ?? ''));
            if ($expression === '') {
                return null;
            }

            $localNow = $now->copy()->setTimezone($timezone);
            $cron = CronExpression::factory($expression);
            $previousRunLocal = Carbon::instance($cron->getPreviousRunDate($localNow, 0, true, $timezone));
            $createdAt = $reminder->created_at !== null ? Carbon::parse((string) $reminder->created_at, 'UTC') : null;

            if ($createdAt !== null && $previousRunLocal->copy()->setTimezone('UTC')->lessThan($createdAt)) {
                return null;
            }

            return $previousRunLocal->format('Y-m-d');
        }

        return $now->copy()->setTimezone($timezone)->format('Y-m-d');
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function isWordModePendingForGroupId(int $groupId, string $targetDate, array $meta): bool
    {
        $sentByGroup = $meta['word_mode_last_sent_by_group'] ?? [];

        if (! is_array($sentByGroup)) {
            return true;
        }

        $lastSentDate = trim((string) ($sentByGroup[(string) $groupId] ?? ''));

        return $lastSentDate !== $targetDate;
    }

    private function isDue(ReligionReminder $reminder, Carbon $now): bool
    {
        $meta = is_array($reminder->meta_json) ? $reminder->meta_json : [];
        $lastRaw = (string) ($meta['last_dispatched_at'] ?? '');
        $last = $lastRaw !== '' ? Carbon::parse($lastRaw, 'UTC') : null;
        $timezone = $this->resolveReminderTimezone($reminder);

        if ((string) $reminder->frequency_mode === 'interval') {
            $minutes = max(1, (int) $reminder->frequency_value);

            if ($last === null) {
                return true;
            }

            return $now->greaterThanOrEqualTo($last->copy()->addMinutes($minutes));
        }

        if ((string) $reminder->frequency_mode !== 'cron') {
            return false;
        }

        $expression = trim((string) ($reminder->frequency_value ?? ''));
        if ($expression === '') {
            return false;
        }

        $localNow = $now->copy()->setTimezone($timezone);
        $cron = CronExpression::factory($expression);

        $previousRun = Carbon::instance($cron->getPreviousRunDate($localNow, 0, true, $timezone))->setTimezone('UTC');
        $createdAt = $reminder->created_at !== null ? Carbon::parse((string) $reminder->created_at, 'UTC') : null;

        if ($createdAt !== null && $previousRun->lessThan($createdAt)) {
            return false;
        }

        if ($last !== null && $last->greaterThanOrEqualTo($previousRun)) {
            return false;
        }

        return true;
    }

    private function resolveReminderTimezone(ReligionReminder $reminder): string
    {
        $timezone = trim((string) ($reminder->schedule_timezone ?? ''));

        if ($timezone !== '' && in_array($timezone, \DateTimeZone::listIdentifiers(), true)) {
            return $timezone;
        }

        return 'Europe/Kyiv';
    }

    private function buildGroupMentionPrefix(int $groupLinkId, int $confessionId): string
    {
        $users = UserGroupReligionPreference::query()
            ->join('messenger_users', 'messenger_users.id', '=', 'user_group_religion_preferences.messenger_user_id')
            ->where('user_group_religion_preferences.messenger_group_link_id', $groupLinkId)
            ->where('user_group_religion_preferences.confession_id', $confessionId)
            ->where('user_group_religion_preferences.is_active', true)
            ->select('messenger_users.external_user_id', 'messenger_users.username', 'messenger_users.display_name')
            ->distinct()
            ->get();

        if ($users->isEmpty()) {
            return '';
        }

        $parts = [];

        foreach ($users as $userRow) {
            $externalId = trim((string) ($userRow->external_user_id ?? ''));
            if ($externalId === '') {
                continue;
            }

            $username = trim((string) ($userRow->username ?? ''));
            $parts[] = $username !== '' ? '@'.$username : '@user'.$externalId;
        }

        if ($parts === []) {
            return '';
        }

        return 'Dear '.implode(', ', array_values(array_unique($parts)));
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function shouldDispatchWordOfDayToGroup(ReligionReminder $reminder, int $groupLinkId, Carbon $now, array $meta): bool
    {
        $targetDate = $this->resolveWordModeTargetDate($reminder, $now);

        if ($targetDate === null) {
            return false;
        }

        return $this->isWordModePendingForGroupId($groupLinkId, $targetDate, $meta);
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    private function markWordOfDayDispatchedForGroup(ReligionReminder $reminder, int $groupLinkId, Carbon $now, array $meta): array
    {
        $timezone = $this->resolveReminderTimezone($reminder);
        $today = $now->copy()->setTimezone($timezone)->format('Y-m-d');
        $sentByGroup = $meta['word_mode_last_sent_by_group'] ?? [];

        if (! is_array($sentByGroup)) {
            $sentByGroup = [];
        }

        $sentByGroup[(string) $groupLinkId] = $today;
        $meta['word_mode_last_sent_by_group'] = $sentByGroup;

        return $meta;
    }

    private function buildWordModeMessage(ReligionReminder $reminder): ?string
    {
        $languagePack = $reminder->languagePack;

        if ($languagePack === null) {
            $languagePack = $reminder->confession?->languagePack;
        }

        if ($languagePack === null) {
            return null;
        }

        $packId = (int) ($languagePack->getKey() ?? 0);
        $wordRow = null;

        if ($packId > 0) {
            $wordRow = LanguageWord::query()
                ->where('language_pack_id', $packId)
                ->where('is_active', true)
                ->inRandomOrder()
                ->first(['word', 'translation', 'transcription', 'phrases']);
        }

        if ($wordRow !== null) {
            $word = trim((string) ($wordRow->word ?? ''));
            $translation = trim((string) ($wordRow->translation ?? ''));
            $transcription = trim((string) ($wordRow->transcription ?? ''));
            $phrases = trim((string) ($wordRow->phrases ?? ''));

            if ($word !== '' && $translation !== '' && $transcription !== '') {
                return $this->renderWordMessage(
                    title: trim((string) ($reminder->title ?? '')),
                    languageCode: trim((string) ($languagePack->code ?? '')),
                    word: $word,
                    translation: $translation,
                    transcription: $transcription,
                    phrases: $phrases
                );
            }
        }

        $meta = is_array($languagePack->meta_json) ? $languagePack->meta_json : [];
        $words = $meta['words'] ?? null;

        if (! is_array($words) || $words === []) {
            return null;
        }

        $valid = array_values(array_filter($words, static function (mixed $entry): bool {
            if (! is_array($entry)) {
                return false;
            }

            $word = trim((string) ($entry['word'] ?? ''));
            $translation = trim((string) ($entry['translation'] ?? ''));
            $transcription = trim((string) ($entry['transcription'] ?? ''));

            return $word !== '' && $translation !== '' && $transcription !== '';
        }));

        if ($valid === []) {
            return null;
        }

        $index = random_int(0, count($valid) - 1);
        $selected = $valid[$index];

        $word = trim((string) ($selected['word'] ?? ''));
        $translation = trim((string) ($selected['translation'] ?? ''));
        $transcription = trim((string) ($selected['transcription'] ?? ''));
        $phrases = trim((string) ($selected['phrases'] ?? ''));
        $languageCode = trim((string) ($languagePack->code ?? ''));

        return $this->renderWordMessage(
            title: trim((string) ($reminder->title ?? '')),
            languageCode: $languageCode,
            word: $word,
            translation: $translation,
            transcription: $transcription,
            phrases: $phrases
        );
    }

    private function renderWordMessage(
        string $title,
        string $languageCode,
        string $word,
        string $translation,
        string $transcription,
        string $phrases,
    ): string {
        if ($title === '') {
            $title = 'Word Reminder';
        }

        $languageLine = $languageCode !== '' ? "Language: {$languageCode}\n" : '';
        $phrasesLine = $phrases !== '' ? "\nPhrases:\n{$phrases}" : '';

        return $title."\n"
            .$languageLine
            .'Word: '.$word."\n"
            .'Translation: '.$translation."\n"
            .'Transcription: '.$transcription
            .$phrasesLine;
    }
}
