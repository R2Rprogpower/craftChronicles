<?php

declare(strict_types=1);

namespace App\Modules\Religions\DTO\Payload;

class ReligionReminderPayloadData implements EntityPayloadData
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
        $preset = (string) ($this->validatedPayload['schedule_preset'] ?? 'command');
        $mode = (string) ($this->validatedPayload['frequency_mode'] ?? 'command');

        $frequencyValue = (string) ($this->validatedPayload['frequency_value'] ?? '');

        if ($preset === 'interval') {
            $mode = 'interval';
            $frequencyValue = (string) max(1, min(1440, (int) ($this->validatedPayload['interval_minutes'] ?? 60)));
        } elseif (in_array($preset, ['daily', 'weekly', 'monthly', 'yearly'], true)) {
            $mode = 'cron';
            $frequencyValue = $this->buildCronFromPreset($preset);
        } elseif ($preset === 'command') {
            $mode = 'command';
            $frequencyValue = '';
        }

        if ($mode === 'manual') {
            $frequencyValue = '';
        }

        return [
            'schedule_timezone' => $this->normalizeTimezone($this->validatedPayload['schedule_timezone'] ?? null),
            'confession_id' => (int) $this->validatedPayload['confession_id'],
            'ritual_id' => isset($this->validatedPayload['ritual_id']) ? (int) $this->validatedPayload['ritual_id'] : null,
            'reminder_type_id' => (int) $this->validatedPayload['reminder_type_id'],
            'implementation_id' => isset($this->validatedPayload['implementation_id']) ? (int) $this->validatedPayload['implementation_id'] : null,
            'command_id' => isset($this->validatedPayload['command_id']) ? (int) $this->validatedPayload['command_id'] : null,
            'language_pack_id' => isset($this->validatedPayload['language_pack_id']) ? (int) $this->validatedPayload['language_pack_id'] : null,
            'word_mode' => (bool) ($this->validatedPayload['word_mode'] ?? false),
            'title' => (string) $this->validatedPayload['title'],
            'content_text' => $this->validatedPayload['content_text'] ?? null,
            'text_format' => (string) $this->validatedPayload['text_format'],
            'frequency_mode' => $mode,
            'schedule_preset' => $preset,
            'interval_minutes' => isset($this->validatedPayload['interval_minutes']) ? (int) $this->validatedPayload['interval_minutes'] : null,
            'schedule_time' => $this->validatedPayload['schedule_time'] ?? null,
            'schedule_weekday' => $this->validatedPayload['schedule_weekday'] ?? null,
            'schedule_monthday' => isset($this->validatedPayload['schedule_monthday']) ? (int) $this->validatedPayload['schedule_monthday'] : null,
            'schedule_year_month' => isset($this->validatedPayload['schedule_year_month']) ? (int) $this->validatedPayload['schedule_year_month'] : null,
            'schedule_year_day' => isset($this->validatedPayload['schedule_year_day']) ? (int) $this->validatedPayload['schedule_year_day'] : null,
            'frequency_value' => $frequencyValue !== '' ? $frequencyValue : null,
            'command_trigger' => $this->validatedPayload['command_trigger'] ?? null,
            'meta_json' => $this->validatedPayload['meta_json'] ?? null,
            'is_active' => (bool) ($this->validatedPayload['is_active'] ?? true),
        ];
    }

    private function buildCronFromPreset(string $preset): string
    {
        $time = (string) ($this->validatedPayload['schedule_time'] ?? '08:00');
        [$hour, $minute] = array_pad(explode(':', $time, 2), 2, '00');
        $h = max(0, min(23, (int) $hour));
        $m = max(0, min(59, (int) $minute));

        if ($preset === 'daily') {
            return sprintf('%d %d * * *', $m, $h);
        }

        if ($preset === 'weekly') {
            $map = ['sun' => 0, 'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6];
            $dow = $map[(string) ($this->validatedPayload['schedule_weekday'] ?? 'mon')] ?? 1;

            return sprintf('%d %d * * %d', $m, $h, $dow);
        }

        if ($preset === 'monthly') {
            $day = max(1, min(31, (int) ($this->validatedPayload['schedule_monthday'] ?? 1)));

            return sprintf('%d %d %d * *', $m, $h, $day);
        }

        $month = max(1, min(12, (int) ($this->validatedPayload['schedule_year_month'] ?? 1)));
        $day = max(1, min(31, (int) ($this->validatedPayload['schedule_year_day'] ?? 1)));

        return sprintf('%d %d %d %d *', $m, $h, $day, $month);
    }

    private function normalizeTimezone(mixed $timezone): string
    {
        if (is_string($timezone) && in_array($timezone, \DateTimeZone::listIdentifiers(), true)) {
            return $timezone;
        }

        return 'Europe/Kyiv';
    }
}
