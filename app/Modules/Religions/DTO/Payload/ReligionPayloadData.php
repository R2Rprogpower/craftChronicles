<?php

declare(strict_types=1);

namespace App\Modules\Religions\DTO\Payload;

class ReligionPayloadData implements EntityPayloadData
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description,
        public readonly string $descriptionFormat,
        public readonly bool $isActive,
    ) {}

    /**
     * @param  array<string, mixed>  $validatedPayload
     */
    public static function fromValidated(array $validatedPayload): self
    {
        return new self(
            name: (string) $validatedPayload['name'],
            slug: (string) $validatedPayload['slug'],
            description: isset($validatedPayload['description']) ? (string) $validatedPayload['description'] : null,
            descriptionFormat: (string) ($validatedPayload['description_format'] ?? 'plain'),
            isActive: (bool) ($validatedPayload['is_active'] ?? true),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'description_format' => $this->descriptionFormat,
            'is_active' => $this->isActive,
        ];
    }
}
