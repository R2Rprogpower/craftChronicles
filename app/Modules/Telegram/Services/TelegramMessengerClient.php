<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Services;

use App\Modules\Messenger\Contracts\MessengerClientInterface;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\File;
use RuntimeException;

class TelegramMessengerClient implements MessengerClientInterface
{
    public function __construct(
        private readonly HttpFactory $http
    ) {}

    public function getIdentity(string $token): array
    {
        return $this->callTelegram($token, 'getMe');
    }

    public function sendMessage(string $token, string $chatId, string $text): array
    {
        return $this->callTelegram($token, 'sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }

    public function sendDocument(string $token, string $chatId, string $filePath, ?string $caption = null): array
    {
        if (! is_file($filePath) || ! is_readable($filePath)) {
            throw new RuntimeException('Telegram document file is missing or unreadable: '.$filePath);
        }

        $baseUrl = rtrim((string) config('messengers.telegram.base_url'), '/');
        $request = $this->http
            ->timeout((int) config('messengers.telegram.timeout_seconds', 10))
            ->acceptJson()
            ->attach(
                'document',
                File::get($filePath),
                basename($filePath)
            );

        $payload = [
            'chat_id' => $chatId,
        ];

        if ($caption !== null && trim($caption) !== '') {
            $payload['caption'] = $caption;
        }

        $response = $request->post("{$baseUrl}/bot{$token}/sendDocument", $payload);

        if (! $response->ok()) {
            throw new RuntimeException('Telegram API HTTP error: '.$response->status());
        }

        /** @var array<string, mixed> $decoded */
        $decoded = $response->json();

        if (($decoded['ok'] ?? false) !== true) {
            $description = (string) ($decoded['description'] ?? 'Unknown Telegram API error');
            throw new RuntimeException('Telegram API error: '.$description);
        }

        return $decoded;
    }

    public function getUpdates(string $token, ?int $offset = null, int $limit = 100): array
    {
        $payload = [
            'timeout' => 1,
            'limit' => $limit,
            'allowed_updates' => ['message', 'my_chat_member'],
        ];

        if ($offset !== null) {
            $payload['offset'] = $offset;
        }

        $response = $this->callTelegram($token, 'getUpdates', $payload);

        return is_array($response['result'] ?? null) ? $response['result'] : [];
    }

    public function setWebhook(string $token, string $url): array
    {
        return $this->callTelegram($token, 'setWebhook', [
            'url' => $url,
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function callTelegram(string $token, string $method, array $payload = []): array
    {
        $baseUrl = rtrim((string) config('messengers.telegram.base_url'), '/');

        $response = $this->http
            ->timeout((int) config('messengers.telegram.timeout_seconds', 10))
            ->acceptJson()
            ->asJson()
            ->post("{$baseUrl}/bot{$token}/{$method}", $payload);

        if (! $response->ok()) {
            throw new RuntimeException('Telegram API HTTP error: '.$response->status());
        }

        /** @var array<string, mixed> $decoded */
        $decoded = $response->json();

        if (($decoded['ok'] ?? false) !== true) {
            $description = (string) ($decoded['description'] ?? 'Unknown Telegram API error');
            throw new RuntimeException('Telegram API error: '.$description);
        }

        return $decoded;
    }
}
