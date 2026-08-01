<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Services;

use App\Modules\Messenger\Models\MessengerBot;
use App\Modules\Messenger\Models\MessengerGroupLink;
use App\Modules\Messenger\Models\MessengerUser;
use App\Modules\Messenger\Services\MessengerClientRegistry;
use App\Modules\Religions\Models\Confession;
use App\Modules\Religions\Models\Religion;
use App\Modules\Religions\Models\ReligionCommand;
use App\Modules\Religions\Models\ReligionReminder;
use App\Modules\Religions\Models\UserGroupReligionPreference;
use App\Modules\Telegram\Contracts\TelegramCommandHandlerInterface;
use App\Modules\Telegram\DTO\TelegramCommandContext;
use App\Modules\Telegram\Services\Handlers\EchoReminderContentHandler;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;

class TelegramCommandDispatchService
{
    public function __construct(
        private readonly MessengerClientRegistry $clientRegistry,
        private readonly TelegramGroupReligionInteractionService $groupReligionInteractionService,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function dispatchIfSupported(MessengerBot $bot, array $payload): void
    {
        $message = Arr::get($payload, 'message');
        if (! is_array($message)) {
            return;
        }

        $text = trim((string) Arr::get($message, 'text', ''));
        if ($text === '') {
            return;
        }

        $chat = Arr::get($message, 'chat');
        $from = Arr::get($message, 'from');

        if (! is_array($chat) || ! is_array($from)) {
            return;
        }

        $chatId = trim((string) ($chat['id'] ?? ''));
        $chatType = trim((string) ($chat['type'] ?? ''));
        $externalUserId = trim((string) ($from['id'] ?? ''));

        if ($chatId === '' || $externalUserId === '') {
            return;
        }

        [$trigger, $args] = $this->parseCommand($text);
        if ($trigger === '') {
            return;
        }

        $messengerUser = MessengerUser::query()->updateOrCreate(
            [
                'driver' => $bot->driver,
                'external_user_id' => $externalUserId,
            ],
            [
                'username' => Arr::get($from, 'username'),
                'display_name' => trim((string) ((string) Arr::get($from, 'first_name', '').' '.(string) Arr::get($from, 'last_name', ''))),
                'language_code' => Arr::get($from, 'language_code'),
                'meta_json' => [
                    'is_bot' => (bool) Arr::get($from, 'is_bot', false),
                ],
                'is_active' => true,
            ]
        );

        $groupLink = MessengerGroupLink::query()
            ->where('messenger_bot_id', $bot->id)
            ->where('external_chat_id', $chatId)
            ->where('is_active', true)
            ->first();

        if ($groupLink === null && in_array($chatType, ['group', 'supergroup'], true)) {
            /** @var MessengerGroupLink $groupLink */
            $groupLink = MessengerGroupLink::query()->updateOrCreate(
                [
                    'messenger_bot_id' => $bot->id,
                    'external_chat_id' => $chatId,
                ],
                [
                    'title' => trim((string) ($chat['title'] ?? 'Telegram Group')),
                    'is_active' => true,
                ]
            );
        }

        $builtInReply = $this->groupReligionInteractionService
            ->handleBuiltInCommand($trigger, $args, $messengerUser, $groupLink);

        if ($builtInReply !== null && $builtInReply !== '') {
            $this->sendReplyMessages($bot, $chatId, $builtInReply);

            if ($trigger === '/aboutme') {
                $this->sendAboutMeAttachments($bot, $chatId);
            }

            return;
        }

        if ($groupLink === null) {
            return;
        }

        /** @var EloquentCollection<int, UserGroupReligionPreference> $preferences */
        $preferences = UserGroupReligionPreference::query()
            ->where('messenger_user_id', $messengerUser->id)
            ->where('messenger_group_link_id', $groupLink->id)
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->get();

        if ($preferences->isEmpty()) {
            return;
        }

        /** @var EloquentCollection<int, ReligionCommand> $commands */
        $commands = ReligionCommand::query()
            ->whereIn('confession_id', $preferences->pluck('confession_id')->all())
            ->where('is_active', true)
            ->get();

        if ($commands->isEmpty()) {
            return;
        }

        $command = null;
        $preference = null;

        foreach ($preferences as $joinedPreference) {
            $matchedCommand = $commands->first(function (ReligionCommand $candidate) use ($joinedPreference, $trigger, $args): bool {
                if ((int) $candidate->confession_id !== (int) $joinedPreference->confession_id) {
                    return false;
                }

                return $this->matchesStoredTrigger((string) $candidate->trigger, $trigger, $args);
            });

            if ($matchedCommand === null) {
                continue;
            }

            $command = $matchedCommand;
            $preference = $joinedPreference;
            break;
        }

        if ($command === null || $preference === null) {
            return;
        }

        $reminder = ReligionReminder::query()
            ->with('implementation')
            ->where('command_id', $command->id)
            ->where('is_active', true)
            ->orderBy('id')
            ->first();

        $context = new TelegramCommandContext(
            bot: $bot,
            chatId: $chatId,
            chatType: $chatType,
            messageText: $text,
            trigger: $trigger,
            args: $args,
            messengerUser: $messengerUser,
            groupLink: $groupLink,
            preference: $preference,
            command: $command,
            reminder: $reminder,
            payload: $payload,
        );

        $handlerClass = $this->resolveHandlerClass($command->command_key, $reminder?->implementation?->handler_class);
        $replyText = $this->executeHandler($handlerClass, $context);

        if ($replyText === '') {
            return;
        }

        if ($reminder !== null && $groupLink !== null) {
            $mentionPrefix = $this->buildGroupMentionPrefix((int) $groupLink->id, (int) $command->confession_id);
            if ($mentionPrefix !== '') {
                $replyText = $mentionPrefix."\n\n".$replyText;
            }
        }

        $this->sendReplyMessages($bot, $chatId, $replyText);
    }

    private function sendReplyMessages(MessengerBot $bot, string $chatId, string $replyText): void
    {
        foreach ($this->splitTelegramMessage($replyText) as $chunk) {
            $this->clientRegistry
                ->forDriver($bot->driver)
                ->sendMessage((string) $bot->bot_token, $chatId, $chunk);
        }
    }

    private function sendAboutMeAttachments(MessengerBot $bot, string $chatId): void
    {
        $paths = $this->groupReligionInteractionService->aboutMeAttachmentPaths();

        foreach ($paths as $index => $path) {
            $caption = $index === 0 ? 'AboutMe Markdown attachments' : null;

            $this->clientRegistry
                ->forDriver($bot->driver)
                ->sendDocument((string) $bot->bot_token, $chatId, $path, $caption);
        }
    }

    /**
     * @return array<int, string>
     */
    private function splitTelegramMessage(string $text, int $maxLength = 3500): array
    {
        $text = trim(str_replace(["\r\n", "\r"], "\n", $text));
        if ($text === '') {
            return [];
        }

        if (mb_strlen($text) <= $maxLength) {
            return [$text];
        }

        $chunks = [];
        $remaining = $text;

        while (mb_strlen($remaining) > $maxLength) {
            $slice = mb_substr($remaining, 0, $maxLength);
            $breakPos = mb_strrpos($slice, "\n\n");

            if ($breakPos === false || $breakPos < (int) floor($maxLength * 0.5)) {
                $breakPos = mb_strrpos($slice, "\n");
            }

            if ($breakPos === false || $breakPos < (int) floor($maxLength * 0.5)) {
                $breakPos = mb_strrpos($slice, ' ');
            }

            if ($breakPos === false || $breakPos < 1) {
                $breakPos = $maxLength;
            }

            $chunk = trim(mb_substr($remaining, 0, $breakPos));
            if ($chunk !== '') {
                $chunks[] = $chunk;
            }

            $remaining = ltrim(mb_substr($remaining, $breakPos));
        }

        $remaining = trim($remaining);
        if ($remaining !== '') {
            $chunks[] = $remaining;
        }

        return $chunks;
    }

    /**
     * @return array{0:string,1:array<int, string>}
     */
    private function parseCommand(string $text): array
    {
        $parts = preg_split('/\s+/', $text) ?: [];
        if ($parts === []) {
            return ['', []];
        }

        $commandIndex = null;

        foreach ($parts as $index => $part) {
            if (Str::startsWith((string) $part, '/')) {
                $commandIndex = $index;
                break;
            }
        }

        if ($commandIndex === null) {
            return ['', []];
        }

        $raw = (string) ($parts[$commandIndex] ?? '');
        $commandToken = Str::before($raw, '@');
        [$trigger, $tokenArgs] = $this->parseTokenCommand((string) $commandToken);

        if ($trigger === '' || ! Str::startsWith($trigger, '/')) {
            return ['', []];
        }

        $spaceArgs = array_values(
            array_filter(
                array_slice($parts, $commandIndex + 1),
                static fn (string $arg): bool => $arg !== ''
            )
        );

        $args = array_values(array_merge($tokenArgs, $spaceArgs));

        return $this->expandCommandShortcut($trigger, $args);
    }

    /**
     * @return array{0:string,1:array<int, string>}
     */
    private function parseTokenCommand(string $commandToken): array
    {
        $token = trim($commandToken);
        if ($token === '' || ! Str::startsWith($token, '/')) {
            return ['', []];
        }

        $withoutSlash = (string) Str::after($token, '/');
        if ($withoutSlash === '') {
            return ['', []];
        }

        $commandName = $withoutSlash;
        $tokenArgs = [];

        if (Str::contains($withoutSlash, '_')) {
            [$commandName, $argPayload] = array_pad(explode('_', $withoutSlash, 2), 2, '');
            if ($argPayload !== '') {
                $tokenArgs = array_values(
                    array_filter(
                        explode('__', $argPayload),
                        static fn (string $value): bool => trim($value) !== ''
                    )
                );
            }
        }

        return ['/'.Str::lower(trim($commandName)), $tokenArgs];
    }

    /**
     * @param  array<int, string>  $args
     * @return array{0:string,1:array<int, string>}
     */
    private function expandCommandShortcut(string $trigger, array $args): array
    {
        if ($trigger === '/religionlist') {
            return ['/religion', ['list']];
        }

        if ($trigger === '/rituallist') {
            return ['/ritual', ['list']];
        }

        if ($trigger === '/confessiondescribe') {
            return ['/confession', ['describe', (string) ($args[0] ?? '')]];
        }

        if ($trigger === '/confessionleave') {
            return ['/confession', ['leave', (string) ($args[0] ?? '')]];
        }

        if ($trigger === '/religiondescribe') {
            $religionToken = (string) ($args[0] ?? '');
            $religionSlug = $this->findReligionSlugByToken($religionToken);

            if ($religionSlug === null) {
                return ['/religion', ['describe', $religionToken]];
            }

            return ['/religion', ['describe', $religionSlug]];
        }

        if ($trigger === '/religionchoose') {
            $religionToken = (string) ($args[0] ?? '');
            $religionSlug = $this->findReligionSlugByToken($religionToken);

            if ($religionSlug === null) {
                return ['/religion', ['choose', $religionToken]];
            }

            $expandedArgs = ['choose', $religionSlug];
            $confessionToken = (string) ($args[1] ?? '');

            if ($confessionToken !== '') {
                $confessionSlug = $this->findConfessionSlugByToken($religionSlug, $confessionToken);
                $expandedArgs[] = $confessionSlug ?? $confessionToken;
            }

            return ['/religion', $expandedArgs];
        }

        return [$trigger, $args];
    }

    /**
     * @param  array<int, string>  $incomingArgs
     */
    private function matchesStoredTrigger(string $storedTrigger, string $incomingTrigger, array $incomingArgs): bool
    {
        $tokens = preg_split('/\s+/', strtolower(trim($storedTrigger))) ?: [];
        if ($tokens === []) {
            return false;
        }

        $baseTrigger = (string) ($tokens[0] ?? '');
        $fixedArgs = array_values(array_slice($tokens, 1));

        if ($incomingTrigger === $baseTrigger && $this->startsWithArgs($incomingArgs, $fixedArgs)) {
            return true;
        }

        $camelAlias = $this->buildCamelAliasFromTokens($tokens);

        return $camelAlias !== '' && $incomingTrigger === $camelAlias;
    }

    /**
     * @param  array<int, string>  $incomingArgs
     * @param  array<int, string>  $prefixArgs
     */
    private function startsWithArgs(array $incomingArgs, array $prefixArgs): bool
    {
        if (count($prefixArgs) > count($incomingArgs)) {
            return false;
        }

        foreach ($prefixArgs as $index => $prefixArg) {
            if (strtolower(trim((string) ($incomingArgs[$index] ?? ''))) !== strtolower(trim($prefixArg))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int, string>  $tokens
     */
    private function buildCamelAliasFromTokens(array $tokens): string
    {
        $normalized = [];

        foreach ($tokens as $index => $token) {
            $raw = $index === 0 ? ltrim($token, '/') : $token;
            $chunk = (string) preg_replace('/[^a-z0-9]+/i', ' ', strtolower(trim($raw)));
            $parts = array_values(array_filter(explode(' ', $chunk), static fn (string $part): bool => $part !== ''));

            foreach ($parts as $part) {
                $normalized[] = $part;
            }
        }

        if ($normalized === []) {
            return '';
        }

        $camel = (string) array_shift($normalized);
        foreach ($normalized as $part) {
            $camel .= ucfirst($part);
        }

        return '/'.strtolower($camel);
    }

    private function findReligionSlugByToken(string $token): ?string
    {
        $normalizedToken = $this->normalizeCommandToken($token);
        if ($normalizedToken === '') {
            return null;
        }

        $religions = Religion::query()
            ->where('is_active', true)
            ->get(['slug']);

        foreach ($religions as $religion) {
            $slug = (string) ($religion->slug ?? '');
            if ($slug === '') {
                continue;
            }

            if ($this->normalizeCommandToken($slug) === $normalizedToken) {
                return $slug;
            }
        }

        return null;
    }

    private function findConfessionSlugByToken(string $religionSlug, string $token): ?string
    {
        $normalizedToken = $this->normalizeCommandToken($token);
        if ($normalizedToken === '') {
            return null;
        }

        $religion = Religion::query()
            ->where('slug', $religionSlug)
            ->where('is_active', true)
            ->first();

        if ($religion === null) {
            return null;
        }

        $confessions = Confession::query()
            ->where('religion_id', $religion->id)
            ->where('is_active', true)
            ->get(['slug']);

        foreach ($confessions as $confession) {
            $slug = (string) ($confession->slug ?? '');
            if ($slug === '') {
                continue;
            }

            if ($this->normalizeCommandToken($slug) === $normalizedToken) {
                return $slug;
            }
        }

        return null;
    }

    private function normalizeCommandToken(string $value): string
    {
        $token = (string) preg_replace('/[^a-z0-9]+/i', '_', strtolower(trim($value)));

        return trim($token, '_');
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

    private function resolveHandlerClass(string $commandKey, ?string $implementationHandlerClass): string
    {
        if (is_string($implementationHandlerClass) && trim($implementationHandlerClass) !== '') {
            return trim($implementationHandlerClass);
        }

        $map = config('religion_command_handlers.map', []);
        $mapped = is_array($map) ? Arr::get($map, trim($commandKey)) : null;
        if (is_string($mapped) && trim($mapped) !== '') {
            return trim($mapped);
        }

        return EchoReminderContentHandler::class;
    }

    private function executeHandler(string $handlerClass, TelegramCommandContext $context): string
    {
        if (! class_exists($handlerClass)) {
            throw new RuntimeException("Handler class '{$handlerClass}' was not found.");
        }

        $handler = app($handlerClass);

        if ($handler instanceof TelegramCommandHandlerInterface) {
            return trim($handler->handle($context));
        }

        if (method_exists($handler, 'handleTelegramCommand')) {
            $result = $handler->handleTelegramCommand($context);

            return is_string($result) ? trim($result) : '';
        }

        if (is_callable($handler)) {
            $result = $handler($context);

            return is_string($result) ? trim($result) : '';
        }

        throw new RuntimeException("Handler class '{$handlerClass}' does not support Telegram command handling.");
    }
}
