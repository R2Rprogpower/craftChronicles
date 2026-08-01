<?php

declare(strict_types=1);

namespace App\Modules\Religions\Services;

use App\Modules\Religions\DTO\Payload\ConfessionPayloadData;
use App\Modules\Religions\DTO\Payload\EntityPayloadData;
use App\Modules\Religions\DTO\Payload\LanguagePackPayloadData;
use App\Modules\Religions\DTO\Payload\LanguageWordPayloadData;
use App\Modules\Religions\DTO\Payload\ReligionCommandPayloadData;
use App\Modules\Religions\DTO\Payload\ReligionPayloadData;
use App\Modules\Religions\DTO\Payload\ReligionReminderPayloadData;
use App\Modules\Religions\DTO\Payload\ReminderDeliveryTargetPayloadData;
use App\Modules\Religions\DTO\Payload\ReminderImplementationPayloadData;
use App\Modules\Religions\DTO\Payload\ReminderRunPayloadData;
use App\Modules\Religions\DTO\Payload\ReminderTypePayloadData;
use App\Modules\Religions\DTO\Payload\RitualPayloadData;
use App\Modules\Religions\DTO\Payload\UserGroupReligionPreferencePayloadData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class ReligionsPersistenceService
{
    public function __construct(
        private readonly ReligionsEntityRegistry $registry,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function create(string $entity, array $payload): array
    {
        $model = $this->registry->resolveModel($entity);
        $data = $this->mapToEntityPayloadDto($entity, $this->validatePayload($entity, $payload))->toArray();

        /** @var Model $created */
        $created = $model::query()->create($data);

        return $created->toArray();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function update(string $entity, int $id, array $payload): array
    {
        $model = $this->registry->resolveModel($entity);
        $data = $this->mapToEntityPayloadDto($entity, $this->validatePayload($entity, $payload, $id))->toArray();

        /** @var Model $row */
        $row = $model::query()->findOrFail($id);
        $row->fill($data);
        $row->save();

        return $row->toArray();
    }

    public function delete(string $entity, int $id): void
    {
        $model = $this->registry->resolveModel($entity);
        $row = $model::query()->findOrFail($id);
        $row->delete();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function validatePayload(string $entity, array $payload, ?int $id = null): array
    {
        $rules = match ($entity) {
            'religions' => [
                'name' => ['required', 'string', 'max:255'],
                'slug' => ['required', 'string', 'max:255', Rule::unique('religions', 'slug')->ignore($id)],
                'description' => ['nullable', 'string'],
                'description_format' => ['sometimes', Rule::in(['plain', 'markdown', 'wysiwyg'])],
                'is_active' => ['sometimes', 'boolean'],
            ],
            'confessions' => [
                'religion_id' => ['required', 'integer', 'exists:religions,id'],
                'name' => ['required', 'string', 'max:255'],
                'slug' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'description_format' => ['sometimes', Rule::in(['plain', 'markdown', 'wysiwyg'])],
                'welcome_message' => ['nullable', 'string'],
                'language_pack_id' => ['nullable', 'integer', 'exists:language_packs,id'],
                'is_active' => ['sometimes', 'boolean'],
            ],
            'rituals' => [
                'confession_id' => ['required', 'integer', 'exists:confessions,id'],
                'ritual_key' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'description_format' => ['sometimes', Rule::in(['plain', 'markdown', 'wysiwyg'])],
                'is_active' => ['sometimes', 'boolean'],
            ],
            'reminder_types' => [
                'type_key' => ['required', 'string', 'max:255', Rule::unique('reminder_types', 'type_key')->ignore($id)],
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'is_builtin' => ['sometimes', 'boolean'],
            ],
            'reminder_implementations' => [
                'reminder_type_id' => ['required', 'integer', 'exists:reminder_types,id'],
                'implementation_key' => ['required', 'string', 'max:255'],
                'implementation_mode' => ['required', Rule::in(['manual', 'auto'])],
                'handler_class' => ['nullable', 'string', 'max:255'],
                'config_json' => ['nullable', 'array'],
                'is_active' => ['sometimes', 'boolean'],
            ],
            'religion_commands' => [
                'confession_id' => ['required', 'integer', 'exists:confessions,id'],
                'command_key' => ['required', 'string', 'max:255'],
                'trigger' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'is_active' => ['sometimes', 'boolean'],
            ],
            'religion_reminders' => [
                'confession_id' => ['required', 'integer', 'exists:confessions,id'],
                'ritual_id' => ['nullable', 'integer', 'exists:rituals,id'],
                'reminder_type_id' => ['required', 'integer', 'exists:reminder_types,id'],
                'implementation_id' => ['nullable', 'integer', 'exists:reminder_implementations,id'],
                'command_id' => ['nullable', 'integer', 'exists:religion_commands,id'],
                'language_pack_id' => ['nullable', 'integer', 'exists:language_packs,id'],
                'word_mode' => ['sometimes', 'boolean'],
                'title' => ['required', 'string', 'max:255'],
                'content_text' => ['nullable', 'string'],
                'text_format' => ['required', Rule::in(['plain', 'markdown', 'wysiwyg'])],
                'frequency_mode' => ['required', Rule::in(['manual', 'cron', 'interval', 'command'])],
                'schedule_preset' => ['sometimes', Rule::in(['command', 'interval', 'daily', 'weekly', 'monthly', 'yearly', 'custom_cron'])],
                'interval_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
                'schedule_time' => ['nullable', 'date_format:H:i'],
                'schedule_timezone' => ['nullable', 'timezone'],
                'schedule_weekday' => ['nullable', Rule::in(['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'])],
                'schedule_monthday' => ['nullable', 'integer', 'min:1', 'max:31'],
                'schedule_year_month' => ['nullable', 'integer', 'min:1', 'max:12'],
                'schedule_year_day' => ['nullable', 'integer', 'min:1', 'max:31'],
                'frequency_value' => ['nullable', 'string', 'max:255'],
                'command_trigger' => ['nullable', 'string', 'max:255'],
                'meta_json' => ['nullable', 'array'],
                'is_active' => ['sometimes', 'boolean'],
            ],
            'language_packs' => [
                'code' => ['required', 'string', 'max:50', Rule::unique('language_packs', 'code')->ignore($id)],
                'name' => ['required', 'string', 'max:255'],
                'native_name' => ['nullable', 'string', 'max:255'],
                'script' => ['nullable', 'string', 'max:255'],
                'is_active' => ['sometimes', 'boolean'],
                'meta_json' => ['nullable', 'array'],
            ],
            'language_words' => [
                'language_pack_id' => ['required', 'integer', 'exists:language_packs,id'],
                'word' => ['required', 'string', 'max:255'],
                'translation' => ['required', 'string', 'max:255'],
                'transcription' => ['required', 'string', 'max:255'],
                'phrases' => ['nullable', 'string'],
                'meta_json' => ['nullable', 'array'],
                'is_active' => ['sometimes', 'boolean'],
            ],
            'user_group_religion_preferences' => [
                'messenger_user_id' => ['required', 'integer', 'exists:messenger_users,id'],
                'messenger_group_link_id' => ['required', 'integer', 'exists:messenger_group_links,id'],
                'confession_id' => ['required', 'integer', 'exists:confessions,id'],
                'language_pack_id' => ['nullable', 'integer', 'exists:language_packs,id'],
                'ritual_opt_in' => ['sometimes', 'boolean'],
                'is_active' => ['sometimes', 'boolean'],
                'meta_json' => ['nullable', 'array'],
            ],
            'reminder_delivery_targets' => [
                'religion_reminder_id' => ['required', 'integer', 'exists:religion_reminders,id'],
                'target_type' => ['required', Rule::in(['group', 'dm', 'channel'])],
                'messenger_group_link_id' => ['nullable', 'integer', 'exists:messenger_group_links,id'],
                'messenger_user_id' => ['nullable', 'integer', 'exists:messenger_users,id'],
                'channel_key' => ['nullable', 'string', 'max:255'],
                'is_active' => ['sometimes', 'boolean'],
                'meta_json' => ['nullable', 'array'],
            ],
            'reminder_runs' => [
                'religion_reminder_id' => ['required', 'integer', 'exists:religion_reminders,id'],
                'reminder_delivery_target_id' => ['nullable', 'integer', 'exists:reminder_delivery_targets,id'],
                'next_run_at_utc' => ['nullable', 'date'],
                'scheduled_for_utc' => ['required', 'date'],
                'sent_at' => ['nullable', 'date'],
                'status' => ['required', Rule::in(['pending', 'queued', 'sent', 'failed', 'skipped', 'canceled'])],
                'idempotency_key' => ['required', 'string', 'max:255', Rule::unique('reminder_runs', 'idempotency_key')->ignore($id)],
                'attempt_count' => ['sometimes', 'integer', 'min:0'],
                'last_error' => ['nullable', 'string'],
                'payload_json' => ['nullable', 'array'],
            ],
            default => throw new InvalidArgumentException("Unsupported entity '{$entity}'."),
        };

        return Validator::make($payload, $rules)->validate();
    }

    /**
     * @param  array<string, mixed>  $validatedPayload
     */
    private function mapToEntityPayloadDto(string $entity, array $validatedPayload): EntityPayloadData
    {
        return match ($entity) {
            'religions' => ReligionPayloadData::fromValidated($validatedPayload),
            'confessions' => ConfessionPayloadData::fromValidated($validatedPayload),
            'rituals' => RitualPayloadData::fromValidated($validatedPayload),
            'reminder_types' => new ReminderTypePayloadData($validatedPayload),
            'reminder_implementations' => new ReminderImplementationPayloadData($validatedPayload),
            'religion_commands' => new ReligionCommandPayloadData($validatedPayload),
            'religion_reminders' => new ReligionReminderPayloadData($validatedPayload),
            'language_packs' => new LanguagePackPayloadData($validatedPayload),
            'language_words' => new LanguageWordPayloadData($validatedPayload),
            'user_group_religion_preferences' => new UserGroupReligionPreferencePayloadData($validatedPayload),
            'reminder_delivery_targets' => new ReminderDeliveryTargetPayloadData($validatedPayload),
            'reminder_runs' => new ReminderRunPayloadData($validatedPayload),
            default => throw new InvalidArgumentException("Unsupported entity '{$entity}'."),
        };
    }
}
