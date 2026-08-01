<?php

declare(strict_types=1);

namespace App\Modules\Religions\DTO\Payload;

abstract class AbstractEntityPayloadData implements EntityPayloadData
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        protected readonly array $attributes,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
