<?php

declare(strict_types=1);

namespace App\Modules\Religions\DTO\Payload;

class ConfessionPayloadData implements EntityPayloadData
{
	public function __construct(
		public readonly int $religionId,
		public readonly string $name,
		public readonly string $slug,
		public readonly ?string $description,
		public readonly string $descriptionFormat,
		public readonly ?string $welcomeMessage,
		public readonly ?int $languagePackId,
		public readonly bool $isActive,
	) {}

	/**
	 * @param  array<string, mixed>  $validatedPayload
	 */
	public static function fromValidated(array $validatedPayload): self
	{
		return new self(
			religionId: (int) $validatedPayload['religion_id'],
			name: (string) $validatedPayload['name'],
			slug: (string) $validatedPayload['slug'],
			description: isset($validatedPayload['description']) ? (string) $validatedPayload['description'] : null,
			descriptionFormat: (string) ($validatedPayload['description_format'] ?? 'plain'),
			welcomeMessage: isset($validatedPayload['welcome_message']) ? (string) $validatedPayload['welcome_message'] : null,
			languagePackId: isset($validatedPayload['language_pack_id']) ? (int) $validatedPayload['language_pack_id'] : null,
			isActive: (bool) ($validatedPayload['is_active'] ?? true),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		return [
			'religion_id' => $this->religionId,
			'name' => $this->name,
			'slug' => $this->slug,
			'description' => $this->description,
			'description_format' => $this->descriptionFormat,
			'welcome_message' => $this->welcomeMessage,
			'language_pack_id' => $this->languagePackId,
			'is_active' => $this->isActive,
		];
	}
}
