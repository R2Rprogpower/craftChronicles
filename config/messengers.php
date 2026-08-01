<?php

declare(strict_types=1);

return [
    'drivers' => [
        'telegram',
    ],

    'telegram' => [
        'base_url' => env('TELEGRAM_API_BASE_URL', 'https://api.telegram.org'),
        'timeout_seconds' => (int) env('TELEGRAM_HTTP_TIMEOUT_SECONDS', 10),
    ],
];
