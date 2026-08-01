<?php

declare(strict_types=1);

namespace App\Modules\Religions\DTO\Payload;

interface EntityPayloadData
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
