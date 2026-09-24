<?php

declare(strict_types=1);

namespace App\Modules\MovieTierList\Services;

use JsonException;
use RuntimeException;

class MovieTierListContent
{
    /** @return array<string, mixed> */
    public function get(): array
    {
        $path = resource_path('content/movie-tier-list.ru.json');
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read movie tier list content: {$path}");
        }

        try {
            $content = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Movie tier list content is not valid JSON.', previous: $exception);
        }

        if (! is_array($content)) {
            throw new RuntimeException('Movie tier list content must be a JSON object.');
        }

        return $content;
    }
}
