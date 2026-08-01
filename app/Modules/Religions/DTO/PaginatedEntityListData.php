<?php

declare(strict_types=1);

namespace App\Modules\Religions\DTO;

class PaginatedEntityListData
{
    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, mixed>  $meta
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        public readonly array $items,
        public readonly array $meta,
        public readonly array $filters,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'items' => $this->items,
            'meta' => $this->meta,
            'filters' => $this->filters,
        ];
    }
}
