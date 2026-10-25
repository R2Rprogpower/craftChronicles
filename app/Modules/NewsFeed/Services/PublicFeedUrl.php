<?php

declare(strict_types=1);

namespace App\Modules\NewsFeed\Services;

use InvalidArgumentException;

class PublicFeedUrl
{
    public function assertSafe(string $url): void
    {
        $parts = parse_url($url);
        $scheme = is_array($parts) ? ($parts['scheme'] ?? null) : null;
        $host = is_array($parts) ? ($parts['host'] ?? null) : null;

        if (! in_array($scheme, ['http', 'https'], true) || ! is_string($host) || $host === '') {
            throw new InvalidArgumentException('Разрешены только публичные HTTP/HTTPS URL.');
        }

        $normalizedHost = mb_strtolower(rtrim($host, '.'));

        if ($normalizedHost === 'localhost' || str_ends_with($normalizedHost, '.localhost')) {
            throw new InvalidArgumentException('Локальные адреса запрещены.');
        }

        if (filter_var($normalizedHost, FILTER_VALIDATE_IP) !== false) {
            $this->assertPublicIp($normalizedHost);

            return;
        }

        $addresses = gethostbynamel($normalizedHost);

        if ($addresses === false || $addresses === []) {
            throw new InvalidArgumentException('Не удалось определить публичный адрес сайта.');
        }

        foreach ($addresses as $address) {
            $this->assertPublicIp($address);
        }
    }

    private function assertPublicIp(string $address): void
    {
        $valid = filter_var(
            $address,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
        );

        if ($valid === false) {
            throw new InvalidArgumentException('Приватные и служебные адреса запрещены.');
        }
    }
}
