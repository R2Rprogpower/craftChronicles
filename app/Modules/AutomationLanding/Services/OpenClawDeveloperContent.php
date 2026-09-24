<?php

declare(strict_types=1);

namespace App\Modules\AutomationLanding\Services;

use JsonException;
use RuntimeException;

class OpenClawDeveloperContent
{
    /** @return array<string, mixed> */
    public function get(): array
    {
        $path = resource_path('content/openclaw-developer.ru.json');
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read OpenClaw developer landing content: {$path}");
        }

        try {
            $content = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('OpenClaw developer landing content is not valid JSON.', previous: $exception);
        }

        if (! is_array($content)) {
            throw new RuntimeException('OpenClaw developer landing content must be a JSON object.');
        }

        return $content;
    }

    /** @return list<string> */
    public function interestIds(): array
    {
        return array_values(array_map(
            static fn (array $interest): string => (string) $interest['id'],
            $this->get()['form']['interests'] ?? [],
        ));
    }
}
