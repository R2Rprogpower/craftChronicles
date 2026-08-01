<?php

declare(strict_types=1);

namespace App\Modules\Religions\DTO\Payload;

class LanguageWordPayloadData implements EntityPayloadData
{
	/**
	 * @param  array<string, mixed>  $validatedPayload
	 */
	public function __construct(
		private readonly array $validatedPayload,
	) {}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		return [
			'language_pack_id' => (int) $this->validatedPayload['language_pack_id'],
			'word' => (string) $this->validatedPayload['word'],
			'translation' => (string) $this->validatedPayload['translation'],
			'transcription' => (string) $this->validatedPayload['transcription'],
			'phrases' => isset($this->validatedPayload['phrases']) ? (string) $this->validatedPayload['phrases'] : null,
			'meta_json' => $this->validatedPayload['meta_json'] ?? null,
			'is_active' => (bool) ($this->validatedPayload['is_active'] ?? true),
		];
	}
}
