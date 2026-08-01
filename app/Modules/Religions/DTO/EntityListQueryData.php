<?php

declare(strict_types=1);

namespace App\Modules\Religions\DTO;

class EntityListQueryData
{
    public function __construct(
        public readonly int $limit,
        public readonly int $perPage,
        public readonly int $page,
        public readonly string $q,
        public readonly string $status,
        public readonly string $sort,
        public readonly string $dir,
    ) {}

    /**
     * @param  array<string, mixed>  $input
     */
    public static function fromArray(array $input): self
    {
        $limit = (int) ($input['limit'] ?? 50);
        if ($limit < 1 || $limit > 200) {
            $limit = 50;
        }

        $perPage = (int) ($input['per_page'] ?? 20);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 20;
        }

        $page = (int) ($input['page'] ?? 1);
        if ($page < 1) {
            $page = 1;
        }

        $q = trim((string) ($input['q'] ?? ''));

        $status = trim((string) ($input['status'] ?? 'all'));
        if (! in_array($status, ['all', 'active', 'inactive'], true)) {
            $status = 'all';
        }

        $sort = trim((string) ($input['sort'] ?? 'id'));

        $dir = strtolower(trim((string) ($input['dir'] ?? 'desc')));
        if (! in_array($dir, ['asc', 'desc'], true)) {
            $dir = 'desc';
        }

        return new self(
            limit: $limit,
            perPage: $perPage,
            page: $page,
            q: $q,
            status: $status,
            sort: $sort,
            dir: $dir,
        );
    }
}
