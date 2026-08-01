<?php

declare(strict_types=1);

namespace App\Modules\Religions\Services;

use Carbon\Carbon;

class ReligionsPayloadMapperService
{
    public function __construct(
        private readonly ReligionsFormSchemaService $formSchemaService,
    ) {}

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function buildPayloadFromForm(string $entity, array $input): array
    {
        $payload = [];

        foreach ($this->formSchemaService->fieldDefinitions($entity) as $field) {
            $name = (string) $field['name'];
            $type = (string) $field['type'];
            $nullable = (bool) ($field['nullable'] ?? false);
            $raw = $input[$name] ?? null;

            if ($type === 'boolean') {
                $payload[$name] = filter_var($raw, FILTER_VALIDATE_BOOL);
                continue;
            }

            $value = is_string($raw) ? trim($raw) : $raw;

            if ($value === '' || $value === null) {
                if ($nullable) {
                    $payload[$name] = null;
                }

                continue;
            }

            if (in_array($type, ['number', 'select'], true)) {
                if (($name === 'attempt_count' || preg_match('/_id$/', $name) === 1) && is_numeric((string) $value)) {
                    $payload[$name] = (int) $value;
                } else {
                    $payload[$name] = (string) $value;
                }

                continue;
            }

            if ($type === 'json') {
                $decoded = json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR);
                $payload[$name] = $decoded;
                continue;
            }

            if ($type === 'datetime') {
                $payload[$name] = Carbon::parse((string) $value)->toDateTimeString();
                continue;
            }

            $payload[$name] = $value;
        }

        return $payload;
    }
}
