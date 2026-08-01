<?php

declare(strict_types=1);

namespace App\Modules\Religions\DTO;

class TableColumnData
{
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly bool $sortable,
    ) {}

    /**
     * @return array{name:string,label:string,sortable:bool}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'sortable' => $this->sortable,
        ];
    }
}
