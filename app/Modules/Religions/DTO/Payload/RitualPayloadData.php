<?php

declare(strict_types=1);

namespace App\Modules\Religions\DTO\Payload;

class RitualPayloadData implements EntityPayloadData
{
	public function __construct(
		public readonly int $confessionId,
		public readonly string $ritualKey,
		public readonly string $name,
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
			confessionId: (int) $validatedPayload['confession_id'],
			ritualKey: (string) $validatedPayload['ritual_key'],
			name: (string) $validatedPayload['name'],
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
			'confession_id' => $this->confessionId,
			'ritual_key' => $this->ritualKey,
			'name' => $this->name,
			'description' => $this->description,
			'description_format' => $this->descriptionFormat,
			'is_active' => $this->isActive,
		];
	}
}
