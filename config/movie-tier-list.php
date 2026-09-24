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
        '7134cf25fc1220f83bca256eceb8696e4c62a831174fc31809fc19185b684ea5',
    ],
];
