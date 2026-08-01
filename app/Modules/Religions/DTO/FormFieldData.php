<?php

declare(strict_types=1);

namespace App\Modules\Religions\DTO;

class FormFieldData
{
    /**
     * @param  array<int, array{value:string,label:string}>  $options
     */
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly string $type,
        public readonly bool $required = false,
        public readonly bool $nullable = false,
        public readonly array $options = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type,
            'required' => $this->required,
            'nullable' => $this->nullable,
            'options' => $this->options,
        ];
    }
}
