<?php

declare(strict_types=1);

$editorIps = array_values(array_filter(array_map(
    static fn (string $ip): string => trim($ip),
    explode(',', (string) env('MOVIE_TIERLIST_EDITOR_IPS', '')),
)));

return [
    'editor_ips' => $editorIps,
    // The owner's address is stored as a one-way digest, not as personal data in Git.
    'editor_ip_hashes' => [
        '50ea924f3e6e11a792a1e9179ca047913379d7829c6ffa7de73f698fa70a1f46',
    ],
];
