<?php

declare(strict_types=1);

namespace App\Modules\Religions\Services;

use App\Modules\Messenger\Models\MessengerGroupLink;
use App\Modules\Messenger\Services\MessengerClientRegistry;
use App\Modules\Religions\Models\Religion;
use App\Modules\Religions\Models\Ritual;
use App\Modules\Religions\Models\UserGroupReligionPreference;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MorningRitualBroadcastService
{
    public function __construct(
        private readonly MessengerClientRegistry $clientRegistry,
    ) {}

    /**
     * @return array{groups:int,messages:int}
     */
    public function broadcastDaily(): array
    {
        $groups = MessengerGroupLink::query()
            ->with('bot')
            ->where('is_active', true)
            ->get();

        $processedGroups = 0;
        $sentMessages = 0;

        foreach ($groups as $group) {
            $bot = $group->bot;
            if ($bot === null || ! $bot->is_active) {
                continue;
            }

            $processedGroups++;

            $religionRows = UserGroupReligionPreference::query()
                ->select('confessions.religion_id', DB::raw('count(*) as followers'))
                ->join('confessions', 'confessions.id', '=', 'user_group_religion_preferences.confession_id')
                ->where('user_group_religion_preferences.messenger_group_link_id', $group->id)
                ->where('user_group_religion_preferences.is_active', true)
                ->groupBy('confessions.religion_id')
                ->get();

            foreach ($religionRows as $row) {
                $religionId = (int) ($row->religion_id ?? 0);
                $followers = (int) ($row->followers ?? 0);

                if ($religionId < 1 || $followers < 1) {
                    continue;
                }

                $lockKey = sprintf(
                    'morning-ritual-broadcast:%d:%d:%s',
                    $group->id,
                    $religionId,
                    now()->toDateString()
                );

                if (! Cache::add($lockKey, true, now()->endOfDay())) {
                    continue;
                }

                $religion = Religion::query()->where('id', $religionId)->where('is_active', true)->first();
                if ($religion === null) {
                    continue;
                }

                $ritual = Ritual::query()
                    ->where('is_active', true)
                    ->whereHas('confession', static function ($query) use ($religionId): void {
                        $query->where('religion_id', $religionId)->where('is_active', true);
                    })
                    ->orderByRaw("case when lower(ritual_key) like '%morning%' then 0 else 1 end")
                    ->orderBy('id')
                    ->first();

                if ($ritual === null) {
                    continue;
                }

                $description = trim((string) ($ritual->description ?? 'No description provided.'));

                $message = "Morning ritual for {$religion->name}\n"
                    ."Followers in this group: {$followers}\n"
                    ."Ritual: {$ritual->name} ({$ritual->ritual_key})\n\n"
                    ."{$description}";

                $mentionPrefix = $this->buildGroupMentionPrefix((int) $group->id, (int) $religionId);
                if ($mentionPrefix !== '') {
                    $message = $mentionPrefix."\n\n".$message;
                }

                $this->clientRegistry
                    ->forDriver($bot->driver)
                    ->sendMessage((string) $bot->bot_token, (string) $group->external_chat_id, $message);

                $sentMessages++;
            }
        }

        return [
            'groups' => $processedGroups,
            'messages' => $sentMessages,
        ];
    }

    private function buildGroupMentionPrefix(int $groupLinkId, int $religionId): string
    {
        $users = UserGroupReligionPreference::query()
            ->join('confessions', 'confessions.id', '=', 'user_group_religion_preferences.confession_id')
            ->join('messenger_users', 'messenger_users.id', '=', 'user_group_religion_preferences.messenger_user_id')
            ->where('user_group_religion_preferences.messenger_group_link_id', $groupLinkId)
            ->where('user_group_religion_preferences.is_active', true)
            ->where('confessions.religion_id', $religionId)
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
            $displayName = trim((string) ($userRow->display_name ?? ''));
            $label = $username !== '' ? '@'.$username : ($displayName !== '' ? $displayName : 'member');

            $parts[] = $label.' tg://user?id='.$externalId;
        }

        if ($parts === []) {
            return '';
        }

        return 'Members: '.implode(' | ', $parts);
    }
}
