<?php

declare(strict_types=1);

namespace App\Modules\Religions\Services;

use App\Modules\Messenger\Models\MessengerGroupLink;
use App\Modules\Messenger\Models\MessengerUser;
use App\Modules\Religions\DTO\FormFieldData;
use App\Modules\Religions\DTO\SelectOptionData;
use App\Modules\Religions\Models\Confession;
use App\Modules\Religions\Models\LanguagePack;
use App\Modules\Religions\Models\Religion;
use App\Modules\Religions\Models\ReligionCommand;
use App\Modules\Religions\Models\ReligionReminder;
use App\Modules\Religions\Models\ReminderDeliveryTarget;
use App\Modules\Religions\Models\ReminderImplementation;
use App\Modules\Religions\Models\ReminderType;
use App\Modules\Religions\Models\Ritual;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ReligionsFormSchemaService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function fieldDefinitions(string $entity): array
    {
        return match ($entity) {
            'religions' => $this->fields(
                $this->field('name', 'Name', 'text', true),
                $this->field('slug', 'Slug', 'text', true),
                $this->field('description', 'Description', 'textarea'),
                $this->field('description_format', 'Description Format', 'select', true, options: [
                    ['value' => 'plain', 'label' => 'plain'],
                    ['value' => 'markdown', 'label' => 'markdown'],
                    ['value' => 'wysiwyg', 'label' => 'wysiwyg'],
                ]),
                $this->field('is_active', 'Active', 'boolean'),
            ),
            'confessions' => $this->fields(
                $this->field('religion_id', 'Religion', 'select', true, options: $this->religionOptions()),
                $this->field('name', 'Name', 'text', true),
                $this->field('slug', 'Slug', 'text', true),
                $this->field('description', 'Description', 'textarea'),
                $this->field('description_format', 'Description Format', 'select', true, options: [
                    ['value' => 'plain', 'label' => 'plain'],
                    ['value' => 'markdown', 'label' => 'markdown'],
                    ['value' => 'wysiwyg', 'label' => 'wysiwyg'],
                ]),
                $this->field('welcome_message', 'Welcome Message', 'textarea'),
                $this->field('language_pack_id', 'Default Language Pack', 'select', options: $this->languagePackOptions(), nullable: true),
                $this->field('is_active', 'Active', 'boolean'),
            ),
            'rituals' => $this->fields(
                $this->field('confession_id', 'Confession', 'select', true, options: $this->confessionOptions()),
                $this->field('ritual_key', 'Ritual Key', 'text', true),
                $this->field('name', 'Name', 'text', true),
                $this->field('description', 'Description', 'textarea'),
                $this->field('description_format', 'Description Format', 'select', true, options: [
                    ['value' => 'plain', 'label' => 'plain'],
                    ['value' => 'markdown', 'label' => 'markdown'],
                    ['value' => 'wysiwyg', 'label' => 'wysiwyg'],
                ]),
                $this->field('is_active', 'Active', 'boolean'),
            ),
            'reminder_types' => $this->fields(
                $this->field('type_key', 'Type Key', 'text', true),
                $this->field('name', 'Name', 'text', true),
                $this->field('description', 'Description', 'textarea'),
                $this->field('is_builtin', 'Built In', 'boolean'),
            ),
            'reminder_implementations' => $this->fields(
                $this->field('reminder_type_id', 'Reminder Type', 'select', true, options: $this->reminderTypeOptions()),
                $this->field('implementation_key', 'Implementation Key', 'text', true),
                $this->field('implementation_mode', 'Mode', 'select', true, options: [
                    ['value' => 'manual', 'label' => 'manual'],
                    ['value' => 'auto', 'label' => 'auto'],
                ]),
                $this->field('handler_class', 'Handler Class', 'text'),
                $this->field('config_json', 'Config JSON', 'json'),
                $this->field('is_active', 'Active', 'boolean'),
            ),
            'religion_commands' => $this->fields(
                $this->field('confession_id', 'Confession', 'select', true, options: $this->confessionOptions()),
                $this->field('command_key', 'Command Key', 'text', true),
                $this->field('trigger', 'Telegram Trigger', 'text', true),
                $this->field('name', 'Name', 'text', true),
                $this->field('description', 'Description', 'textarea'),
                $this->field('is_active', 'Active', 'boolean'),
            ),
            'religion_reminders' => $this->fields(
                $this->field('confession_id', 'Confession', 'select', true, options: $this->confessionOptions()),
                $this->field('ritual_id', 'Ritual', 'select', options: $this->ritualOptions(), nullable: true),
                $this->field('reminder_type_id', 'Reminder Type', 'select', true, options: $this->reminderTypeOptions()),
                $this->field('implementation_id', 'Implementation', 'select', options: $this->implementationOptions(), nullable: true),
                $this->field('command_id', 'Command', 'select', options: $this->commandOptions(), nullable: true),
                $this->field('language_pack_id', 'Language Pack', 'select', options: $this->languagePackOptions(), nullable: true),
                $this->field('word_mode', 'Post Word + Translation + Transcription', 'boolean'),
                $this->field('title', 'Title', 'text', true),
                $this->field('content_text', 'Content Text', 'textarea'),
                $this->field('text_format', 'Text Format', 'select', true, options: [
                    ['value' => 'plain', 'label' => 'plain'],
                    ['value' => 'markdown', 'label' => 'markdown'],
                    ['value' => 'wysiwyg', 'label' => 'wysiwyg'],
                ]),
                $this->field('frequency_mode', 'Frequency Mode', 'select', true, options: [
                    ['value' => 'manual', 'label' => 'manual'],
                    ['value' => 'cron', 'label' => 'cron'],
                    ['value' => 'interval', 'label' => 'interval'],
                    ['value' => 'command', 'label' => 'command'],
                ]),
                $this->field('schedule_preset', 'Schedule Preset', 'select', true, options: [
                    ['value' => 'command', 'label' => 'command'],
                    ['value' => 'interval', 'label' => 'interval'],
                    ['value' => 'daily', 'label' => 'daily'],
                    ['value' => 'weekly', 'label' => 'weekly'],
                    ['value' => 'monthly', 'label' => 'monthly'],
                    ['value' => 'yearly', 'label' => 'yearly'],
                    ['value' => 'custom_cron', 'label' => 'custom_cron'],
                ]),
                $this->field('interval_minutes', 'Interval Minutes', 'number'),
                $this->field('schedule_time', 'Schedule Time (HH:MM)', 'time'),
                $this->field('schedule_timezone', 'Schedule Timezone', 'select', true, options: $this->timezoneOptions()),
                $this->field('schedule_weekday', 'Schedule Weekday', 'select', options: [
                    ['value' => 'mon', 'label' => 'mon'],
                    ['value' => 'tue', 'label' => 'tue'],
                    ['value' => 'wed', 'label' => 'wed'],
                    ['value' => 'thu', 'label' => 'thu'],
                    ['value' => 'fri', 'label' => 'fri'],
                    ['value' => 'sat', 'label' => 'sat'],
                    ['value' => 'sun', 'label' => 'sun'],
                ], nullable: true),
                $this->field('schedule_monthday', 'Schedule Month Day', 'number'),
                $this->field('schedule_year_month', 'Schedule Year Month', 'number'),
                $this->field('schedule_year_day', 'Schedule Year Day', 'number'),
                $this->field('frequency_value', 'Frequency Value (auto or custom cron)', 'text'),
                $this->field('command_trigger', 'Command Trigger', 'text'),
                $this->field('meta_json', 'Meta JSON', 'json'),
                $this->field('is_active', 'Active', 'boolean'),
            ),
            'language_packs' => $this->fields(
                $this->field('code', 'Code', 'text', true),
                $this->field('name', 'Name', 'text', true),
                $this->field('native_name', 'Native Name', 'text'),
                $this->field('script', 'Script', 'text'),
                $this->field('is_active', 'Active', 'boolean'),
                $this->field('meta_json', 'Meta JSON', 'json'),
            ),
            'language_words' => $this->fields(
                $this->field('language_pack_id', 'Language Pack', 'select', true, options: $this->languagePackOptions()),
                $this->field('word', 'Word', 'text', true),
                $this->field('translation', 'Translation', 'text', true),
                $this->field('transcription', 'Transcription', 'text', true),
                $this->field('phrases', 'Phrases', 'textarea'),
                $this->field('is_active', 'Active', 'boolean'),
                $this->field('meta_json', 'Meta JSON', 'json'),
            ),
            'user_group_religion_preferences' => $this->fields(
                $this->field('messenger_user_id', 'Messenger User', 'select', true, options: $this->messengerUserOptions()),
                $this->field('messenger_group_link_id', 'Group Link', 'select', true, options: $this->groupLinkOptions()),
                $this->field('confession_id', 'Confession', 'select', true, options: $this->confessionOptions()),
                $this->field('language_pack_id', 'Language Pack', 'select', options: $this->languagePackOptions(), nullable: true),
                $this->field('ritual_opt_in', 'Ritual Opt In', 'boolean'),
                $this->field('is_active', 'Active', 'boolean'),
                $this->field('meta_json', 'Meta JSON', 'json'),
            ),
            'reminder_delivery_targets' => $this->fields(
                $this->field('religion_reminder_id', 'Reminder', 'select', true, options: $this->reminderOptions()),
                $this->field('target_type', 'Target Type', 'select', true, options: [
                    ['value' => 'group', 'label' => 'group'],
                    ['value' => 'dm', 'label' => 'dm'],
                    ['value' => 'channel', 'label' => 'channel'],
                ]),
                $this->field('messenger_group_link_id', 'Group Link', 'select', options: $this->groupLinkOptions(), nullable: true),
                $this->field('messenger_user_id', 'Messenger User', 'select', options: $this->messengerUserOptions(), nullable: true),
                $this->field('channel_key', 'Channel Key', 'text'),
                $this->field('is_active', 'Active', 'boolean'),
                $this->field('meta_json', 'Meta JSON', 'json'),
            ),
            'reminder_runs' => $this->fields(
                $this->field('religion_reminder_id', 'Reminder', 'select', true, options: $this->reminderOptions()),
                $this->field('reminder_delivery_target_id', 'Delivery Target', 'select', options: $this->deliveryTargetOptions(), nullable: true),
                $this->field('next_run_at_utc', 'Next Run At (UTC)', 'datetime'),
                $this->field('scheduled_for_utc', 'Scheduled For (UTC)', 'datetime', true),
                $this->field('sent_at', 'Sent At (UTC)', 'datetime'),
                $this->field('status', 'Status', 'select', true, options: [
                    ['value' => 'pending', 'label' => 'pending'],
                    ['value' => 'queued', 'label' => 'queued'],
                    ['value' => 'sent', 'label' => 'sent'],
                    ['value' => 'failed', 'label' => 'failed'],
                    ['value' => 'skipped', 'label' => 'skipped'],
                    ['value' => 'canceled', 'label' => 'canceled'],
                ]),
                $this->field('idempotency_key', 'Idempotency Key', 'text', true),
                $this->field('attempt_count', 'Attempt Count', 'number'),
                $this->field('last_error', 'Last Error', 'textarea'),
                $this->field('payload_json', 'Payload JSON', 'json'),
            ),
            default => throw new InvalidArgumentException("Unsupported entity '{$entity}'."),
        };
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fields(FormFieldData ...$fields): array
    {
        return array_map(
            static fn (FormFieldData $field): array => $field->toArray(),
            $fields
        );
    }

    /**
     * @param  array<int, array{value:string,label:string}>  $options
     */
    private function field(
        string $name,
        string $label,
        string $type,
        bool $required = false,
        array $options = [],
        bool $nullable = false,
    ): FormFieldData {
        return new FormFieldData(
            name: $name,
            label: $label,
            type: $type,
            required: $required,
            nullable: $nullable,
            options: $options,
        );
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function religionOptions(): array
    {
        return $this->buildOptionsFromModel(Religion::class, 'name');
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function confessionOptions(): array
    {
        return $this->buildOptionsFromModel(Confession::class, 'name');
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function ritualOptions(): array
    {
        return $this->buildOptionsFromModel(Ritual::class, 'ritual_key');
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function reminderTypeOptions(): array
    {
        return $this->buildOptionsFromModel(ReminderType::class, 'name');
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function implementationOptions(): array
    {
        return $this->buildOptionsFromModel(ReminderImplementation::class, 'implementation_key');
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function commandOptions(): array
    {
        return $this->buildOptionsFromModel(ReligionCommand::class, 'trigger');
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function languagePackOptions(): array
    {
        return $this->buildOptionsFromModel(LanguagePack::class, 'code');
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function messengerUserOptions(): array
    {
        $rows = MessengerUser::query()
            ->select(['id', 'display_name', 'username', 'external_user_id'])
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $options = [];

        foreach ($rows as $row) {
            /** @var MessengerUser $row */
            $id = (string) $row->getKey();
            $display = trim((string) ($row->getAttribute('display_name') ?? ''));

            if ($display !== '') {
                $label = $display;
            } else {
                $username = trim((string) ($row->getAttribute('username') ?? ''));
                $label = $username !== ''
                    ? $username
                    : (string) ($row->getAttribute('external_user_id') ?? 'user');
            }

            $options[] = (new SelectOptionData(
                value: $id,
                label: "#{$id} {$label}",
            ))->toArray();
        }

        return $options;
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function groupLinkOptions(): array
    {
        $rows = MessengerGroupLink::query()
            ->select(['id', 'title', 'external_chat_id'])
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $options = [];

        foreach ($rows as $row) {
            /** @var MessengerGroupLink $row */
            $id = (string) $row->getKey();
            $title = trim((string) ($row->getAttribute('title') ?? ''));
            $label = $title !== '' ? $title : (string) ($row->getAttribute('external_chat_id') ?? 'group');

            $options[] = (new SelectOptionData(
                value: $id,
                label: "#{$id} {$label}",
            ))->toArray();
        }

        return $options;
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function reminderOptions(): array
    {
        return $this->buildOptionsFromModel(ReligionReminder::class, 'title');
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function deliveryTargetOptions(): array
    {
        return $this->buildOptionsFromModel(ReminderDeliveryTarget::class, 'target_type');
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function timezoneOptions(): array
    {
        $timezones = \DateTimeZone::listIdentifiers();
        $defaultTimezone = 'Europe/Kyiv';

        $options = [
            (new SelectOptionData(value: $defaultTimezone, label: $defaultTimezone))->toArray(),
        ];

        foreach ($timezones as $timezone) {
            if ($timezone === $defaultTimezone) {
                continue;
            }

            $options[] = (new SelectOptionData(value: $timezone, label: $timezone))->toArray();
        }

        return $options;
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @return array<int, array{value:string,label:string}>
     */
    private function buildOptionsFromModel(string $modelClass, string $labelColumn): array
    {
        /** @var iterable<int, Model> $rows */
        $rows = $modelClass::query()
            ->select(['id', $labelColumn])
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $options = [];

        foreach ($rows as $row) {
            $id = (string) $row->getKey();
            $label = (string) ($row->getAttribute($labelColumn) ?? '');

            $options[] = (new SelectOptionData(
                value: $id,
                label: "#{$id} {$label}",
            ))->toArray();
        }

        return $options;
    }
}
