<?php

declare(strict_types=1);

namespace App\Modules\Messenger\Contracts;

interface MessengerClientInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getIdentity(string $token): array;

    /**
     * @return array<string, mixed>
     */
    public function sendMessage(string $token, string $chatId, string $text): array;

    /**
     * @return array<string, mixed>
     */
    public function sendDocument(string $token, string $chatId, string $filePath, ?string $caption = null): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getUpdates(string $token, ?int $offset = null, int $limit = 100): array;

    /**
     * @return array<string, mixed>
     */
    public function setWebhook(string $token, string $url): array;
}
