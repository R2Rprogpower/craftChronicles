<?php

declare(strict_types=1);

namespace App\Modules\Religions\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Religions\DTO\EntityListQueryData;
use App\Modules\Religions\Models\Confession;
use App\Modules\Religions\Models\ReligionCommand;
use App\Modules\Religions\Models\ReligionReminder;
use App\Modules\Religions\Models\ReminderImplementation;
use App\Modules\Religions\Models\ReminderType;
use App\Modules\Religions\Models\Ritual;
use App\Modules\Religions\Services\ReligionBundleTextService;
use App\Modules\Religions\Services\ReligionsCrudService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Throwable;

class ReligionsAdminController extends Controller
{
    public function __construct(
        private readonly ReligionsCrudService $service,
        private readonly ReligionBundleTextService $bundleTextService,
    ) {}

    public function exportBundle(Request $request): Response
    {
        $data = $request->validate([
            'confession_id' => ['nullable', 'integer', 'exists:confessions,id'],
        ]);

        $confessionId = (int) ($data['confession_id'] ?? 2);
        $text = $this->bundleTextService->exportForConfession($confessionId);

        return response($text, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="religion-bundle-confession-'.$confessionId.'.txt"',
        ]);
    }

    public function importBundle(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bundle_text' => ['required', 'string'],
        ]);

        try {
            $result = $this->bundleTextService->importFromText((string) $data['bundle_text']);

            return redirect()->route('religions.admin.command-link')
                ->with('status', sprintf(
                    'Bundle imported. religion_id=%d confession_id=%d rituals=%d reminders=%d words=%d',
                    $result['religion_id'],
                    $result['confession_id'],
                    $result['rituals'],
                    $result['reminders'],
                    $result['words']
                ));
        } catch (Throwable $exception) {
            return redirect()->route('religions.admin.command-link')
                ->withInput()
                ->withErrors(['bundle_import' => $exception->getMessage()]);
        }
    }

    public function index(): RedirectResponse
    {
        $entities = $this->service->entities();

        return redirect()->route('religions.admin.entity', ['entity' => $entities[0]]);
    }

    public function entity(Request $request, string $entity): View
    {
        return $this->renderPage($request, $entity, 'list');
    }

    public function createPage(Request $request, string $entity): View
    {
        return $this->renderPage($request, $entity, 'create');
    }

    public function edit(Request $request, string $entity, int $id): View
    {
        $editRow = $this->service->find($entity, $id);

        abort_if($editRow === null, 404);

        return $this->renderPage($request, $entity, 'edit', $editRow);
    }

    public function commandLinkPage(Request $request): View
    {
        return $this->renderPage($request, 'religion_commands', 'link-command');
    }

    public function reminderWizardPage(Request $request): View
    {
        return $this->renderPage($request, 'religion_commands', 'link-command');
    }

    public function storeEntity(Request $request, string $entity): RedirectResponse
    {
        try {
            $payload = $this->service->buildPayloadFromForm($entity, $request->all());
            $this->service->create($entity, $payload);

            return redirect()->route('religions.admin.entity', ['entity' => $entity])
                ->with('status', 'Created successfully.');
        } catch (Throwable $exception) {
            return redirect()->route('religions.admin.entity', ['entity' => $entity])
                ->withErrors(['crud_form_create' => $exception->getMessage()]);
        }
    }

    public function updateEntity(Request $request, string $entity, int $id): RedirectResponse
    {
        try {
            $payload = $this->service->buildPayloadFromForm($entity, $request->all());
            $this->service->update($entity, $id, $payload);

            return redirect()->route('religions.admin.entity', ['entity' => $entity])
                ->with('status', 'Updated successfully.');
        } catch (Throwable $exception) {
            return redirect()->route('religions.admin.edit', ['entity' => $entity, 'id' => $id])
                ->withErrors(['crud_form_update' => $exception->getMessage()]);
        }
    }

    public function destroyEntity(string $entity, int $id): RedirectResponse
    {
        try {
            $this->service->delete($entity, $id);

            return redirect()->route('religions.admin.entity', ['entity' => $entity])
                ->with('status', 'Deleted successfully.');
        } catch (Throwable $exception) {
            return redirect()->route('religions.admin.entity', ['entity' => $entity])
                ->withErrors(['crud_form_delete' => $exception->getMessage()]);
        }
    }

    public function linkCommand(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'confession_id' => ['required', 'integer', 'exists:confessions,id'],
            'command_key' => ['required', 'string', 'max:255'],
            'tg_command' => ['required', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $commandKey = Str::of((string) $data['command_key'])->trim()->lower()->replace(' ', '-')->value();
        $trigger = Str::start((string) Str::of((string) $data['tg_command'])->trim()->value(), '/');

        try {
            ReligionCommand::query()->updateOrCreate(
                [
                    'confession_id' => (int) $data['confession_id'],
                    'command_key' => $commandKey,
                ],
                [
                    'trigger' => $trigger,
                    'name' => (string) ($data['name'] ?? Str::headline(str_replace('-', ' ', $commandKey))),
                    'description' => $data['description'] ?? null,
                    'is_active' => (bool) ($data['is_active'] ?? true),
                ]
            );

            return redirect()->route('religions.admin.command-link')
                ->with('status', 'Command link saved successfully.');
        } catch (Throwable $exception) {
            return redirect()->route('religions.admin.command-link')
                ->withErrors(['command_link' => $exception->getMessage()]);
        }
    }

    public function reminderWizard(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'confession_id' => ['required', 'integer', 'exists:confessions,id'],
            'command_key' => ['required', 'string', 'max:255'],
            'tg_command' => ['required', 'string', 'max:255'],
            'command_name' => ['nullable', 'string', 'max:255'],
            'command_description' => ['nullable', 'string'],
            'reminder_title' => ['required', 'string', 'max:255'],
            'reminder_text' => ['nullable', 'string'],
            'reminder_type_id' => ['required', 'integer', 'exists:reminder_types,id'],
            'implementation_id' => ['nullable', 'integer', 'exists:reminder_implementations,id'],
            'ritual_id' => ['nullable', 'integer', 'exists:rituals,id'],
            'text_format' => ['required', 'in:plain,markdown,wysiwyg'],
            'schedule_preset' => ['required', 'in:command,interval,daily,weekly,monthly,yearly'],
            'interval_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'schedule_time' => ['nullable', 'date_format:H:i'],
            'schedule_timezone' => ['nullable', 'timezone'],
            'weekly_day' => ['nullable', 'in:mon,tue,wed,thu,fri,sat,sun'],
            'monthly_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'yearly_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'yearly_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $commandKey = Str::of((string) $data['command_key'])->trim()->lower()->replace(' ', '-')->value();
        $trigger = Str::start((string) Str::of((string) $data['tg_command'])->trim()->value(), '/');

        $schedulePreset = (string) $data['schedule_preset'];

        if (! in_array($schedulePreset, ['command', 'interval'], true) && empty($data['schedule_time'])) {
            return redirect()->route('religions.admin.command-link')
                ->withErrors(['reminder_wizard' => 'Schedule time is required for non-command presets.']);
        }

        if ($schedulePreset === 'interval' && empty($data['interval_minutes'])) {
            return redirect()->route('religions.admin.command-link')
                ->withErrors(['reminder_wizard' => 'Interval minutes is required for interval preset.']);
        }

        if ($schedulePreset === 'weekly' && empty($data['weekly_day'])) {
            return redirect()->route('religions.admin.command-link')
                ->withErrors(['reminder_wizard' => 'Weekly day is required for weekly preset.']);
        }

        if ($schedulePreset === 'monthly' && empty($data['monthly_day'])) {
            return redirect()->route('religions.admin.command-link')
                ->withErrors(['reminder_wizard' => 'Monthly day is required for monthly preset.']);
        }

        if ($schedulePreset === 'yearly' && (empty($data['yearly_month']) || empty($data['yearly_day']))) {
            return redirect()->route('religions.admin.command-link')
                ->withErrors(['reminder_wizard' => 'Yearly month and day are required for yearly preset.']);
        }

        if (! empty($data['ritual_id'])) {
            $ritualConfessionId = (int) Ritual::query()
                ->where('id', (int) $data['ritual_id'])
                ->value('confession_id');

            if ($ritualConfessionId !== (int) $data['confession_id']) {
                return redirect()->route('religions.admin.command-link')
                    ->withErrors(['reminder_wizard' => 'Selected ritual must belong to the selected confession.']);
            }
        }

        try {
            DB::transaction(function () use ($data, $commandKey, $trigger): void {
                $command = ReligionCommand::query()->updateOrCreate(
                    [
                        'confession_id' => (int) $data['confession_id'],
                        'command_key' => $commandKey,
                    ],
                    [
                        'trigger' => $trigger,
                        'name' => (string) ($data['command_name'] ?? Str::headline(str_replace('-', ' ', $commandKey))),
                        'description' => $data['command_description'] ?? null,
                        'is_active' => (bool) ($data['is_active'] ?? true),
                    ]
                );

                $ritual = null;

                if (! empty($data['ritual_id'])) {
                    $ritual = Ritual::query()->find((int) $data['ritual_id']);
                }

                $frequencyMode = match ((string) $data['schedule_preset']) {
                    'command' => 'command',
                    'interval' => 'interval',
                    default => 'cron',
                };
                $frequencyValue = $this->buildFrequencyValueFromPreset($data);

                $contentText = $data['reminder_text'] ?? null;

                if ($ritual !== null) {
                    $contentText = (string) ($ritual->description ?? '');
                }

                ReligionReminder::query()->create([
                    'confession_id' => (int) $data['confession_id'],
                    'ritual_id' => isset($data['ritual_id']) ? (int) $data['ritual_id'] : null,
                    'reminder_type_id' => (int) $data['reminder_type_id'],
                    'implementation_id' => isset($data['implementation_id']) ? (int) $data['implementation_id'] : null,
                    'command_id' => (int) $command->getKey(),
                    'title' => (string) $data['reminder_title'],
                    'content_text' => $contentText,
                    'text_format' => (string) $data['text_format'],
                    'frequency_mode' => $frequencyMode,
                    'schedule_preset' => (string) $data['schedule_preset'],
                    'interval_minutes' => isset($data['interval_minutes']) ? (int) $data['interval_minutes'] : null,
                    'schedule_time' => $data['schedule_time'] ?? null,
                    'schedule_timezone' => (string) ($data['schedule_timezone'] ?? 'Europe/Kyiv'),
                    'schedule_weekday' => $data['weekly_day'] ?? null,
                    'schedule_monthday' => isset($data['monthly_day']) ? (int) $data['monthly_day'] : null,
                    'schedule_year_month' => isset($data['yearly_month']) ? (int) $data['yearly_month'] : null,
                    'schedule_year_day' => isset($data['yearly_day']) ? (int) $data['yearly_day'] : null,
                    'frequency_value' => $frequencyValue,
                    'command_trigger' => $trigger,
                    'meta_json' => null,
                    'is_active' => (bool) ($data['is_active'] ?? true),
                ]);
            });

            return redirect()->route('religions.admin.command-link')
                ->with('status', 'Reminder setup created (command + reminder).');
        } catch (Throwable $exception) {
            return redirect()->route('religions.admin.command-link')
                ->withErrors(['reminder_wizard' => $exception->getMessage()]);
        }
    }

    public function setWordModeReminderToNextMinute(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reminder_id' => ['required', 'integer', 'exists:religion_reminders,id'],
        ]);

        $reminder = ReligionReminder::query()->findOrFail((int) $data['reminder_id']);

        if (! (bool) ($reminder->word_mode ?? false)) {
            return redirect()->route('religions.admin.command-link')
                ->withErrors(['word_mode_smoke_test' => 'Selected reminder is not a word_mode reminder.']);
        }

        $timezone = trim((string) ($reminder->schedule_timezone ?? 'Europe/Kyiv'));
        if ($timezone === '' || ! in_array($timezone, \DateTimeZone::listIdentifiers(), true)) {
            $timezone = 'Europe/Kyiv';
        }

        $nextMinuteLocal = now($timezone)->addMinute()->startOfMinute();
        $meta = is_array($reminder->meta_json) ? $reminder->meta_json : [];
        unset($meta['last_dispatched_at'], $meta['word_mode_last_sent_by_group']);

        $reminder->frequency_mode = 'cron';
        $reminder->schedule_preset = 'daily';
        $reminder->schedule_time = $nextMinuteLocal->format('H:i');
        $reminder->schedule_timezone = $timezone;
        $reminder->frequency_value = sprintf(
            '%d %d * * *',
            (int) $nextMinuteLocal->format('i'),
            (int) $nextMinuteLocal->format('H')
        );
        $reminder->meta_json = $meta;
        $reminder->save();

        return redirect()->route('religions.admin.command-link')
            ->with('status', sprintf(
                'Word-of-day smoke test set. reminder_id=%d next_run=%s %s',
                (int) $reminder->getKey(),
                $nextMinuteLocal->format('Y-m-d H:i'),
                $timezone
            ));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function buildFrequencyValueFromPreset(array $data): ?string
    {
        $preset = (string) ($data['schedule_preset'] ?? 'command');

        if ($preset === 'command') {
            return null;
        }

        if ($preset === 'interval') {
            $minutes = max(1, min(1440, (int) ($data['interval_minutes'] ?? 60)));

            return (string) $minutes;
        }

        $time = (string) ($data['schedule_time'] ?? '08:00');
        [$hour, $minute] = array_pad(explode(':', $time, 2), 2, '00');

        $h = max(0, min(23, (int) $hour));
        $m = max(0, min(59, (int) $minute));

        if ($preset === 'daily') {
            return sprintf('%d %d * * *', $m, $h);
        }

        if ($preset === 'weekly') {
            $dowMap = [
                'sun' => 0,
                'mon' => 1,
                'tue' => 2,
                'wed' => 3,
                'thu' => 4,
                'fri' => 5,
                'sat' => 6,
            ];

            $dow = $dowMap[(string) ($data['weekly_day'] ?? 'mon')] ?? 1;

            return sprintf('%d %d * * %d', $m, $h, $dow);
        }

        if ($preset === 'monthly') {
            $day = max(1, min(31, (int) ($data['monthly_day'] ?? 1)));

            return sprintf('%d %d %d * *', $m, $h, $day);
        }

        $month = max(1, min(12, (int) ($data['yearly_month'] ?? 1)));
        $day = max(1, min(31, (int) ($data['yearly_day'] ?? 1)));

        return sprintf('%d %d %d %d *', $m, $h, $day, $month);
    }

    /**
     * @param  array<string, mixed>|null  $editRow
     */
    private function renderPage(Request $request, string $entity, string $mode, ?array $editRow = null): View
    {
        $entities = $this->service->entities();
        abort_unless(in_array($entity, $entities, true), 404);

        $listing = $this->service->listPaginated(
            $entity,
            EntityListQueryData::fromArray($request->query())
        );

        $sampleBundleText = null;
        $userSelections = [];
        $wordModeReminders = [];

        if ($mode === 'link-command') {
            try {
                $sampleBundleText = $this->bundleTextService->exportForConfession(2);
            } catch (Throwable) {
                $sampleBundleText = null;
            }

            $selectionRows = DB::table('user_group_religion_preferences as p')
                ->join('messenger_users as u', 'u.id', '=', 'p.messenger_user_id')
                ->join('messenger_group_links as g', 'g.id', '=', 'p.messenger_group_link_id')
                ->join('confessions as c', 'c.id', '=', 'p.confession_id')
                ->join('religions as r', 'r.id', '=', 'c.religion_id')
                ->where('p.is_active', true)
                ->select([
                    'u.id as user_id',
                    'u.username',
                    'u.display_name',
                    'u.external_user_id',
                    'g.id as group_id',
                    'g.title as group_title',
                    'g.external_chat_id',
                    'r.name as religion_name',
                    'r.slug as religion_slug',
                    'c.name as confession_name',
                    'c.slug as confession_slug',
                ])
                ->orderBy('u.id')
                ->orderBy('g.id')
                ->orderBy('r.name')
                ->orderBy('c.name')
                ->get();

            $grouped = [];

            foreach ($selectionRows as $row) {
                $key = (string) $row->user_id.'|'.(string) $row->group_id;

                if (! isset($grouped[$key])) {
                    $grouped[$key] = [
                        'user_label' => $this->buildUserLabel(
                            username: (string) ($row->username ?? ''),
                            displayName: (string) ($row->display_name ?? ''),
                            externalUserId: (string) ($row->external_user_id ?? '')
                        ),
                        'group_label' => trim((string) ($row->group_title ?? '')) !== ''
                            ? '#'.$row->group_id.' '.trim((string) $row->group_title)
                            : '#'.$row->group_id.' '.(string) ($row->external_chat_id ?? ''),
                        'religions' => [],
                        'confessions' => [],
                    ];
                }

                $grouped[$key]['religions'][] = trim((string) ($row->religion_name ?? '')).' ('.trim((string) ($row->religion_slug ?? '')).')';
                $grouped[$key]['confessions'][] = trim((string) ($row->confession_name ?? '')).' ('.trim((string) ($row->confession_slug ?? '')).')';
            }

            foreach ($grouped as $entry) {
                $userSelections[] = [
                    'user_label' => $entry['user_label'],
                    'group_label' => $entry['group_label'],
                    'religions' => implode(', ', array_values(array_unique($entry['religions']))),
                    'confessions' => implode(', ', array_values(array_unique($entry['confessions']))),
                ];
            }

            $wordModeReminders = ReligionReminder::query()
                ->with('confession:id,name')
                ->where('word_mode', true)
                ->where('is_active', true)
                ->orderBy('id')
                ->get(['id', 'confession_id', 'title', 'schedule_time', 'schedule_timezone', 'frequency_value']);
        }

        return view('religions-admin', [
            'mode' => $mode,
            'entities' => $entities,
            'entityLabels' => $this->service->entityLabels(),
            'entity' => $entity,
            'rows' => Arr::get($listing, 'items', []),
            'rowsMeta' => Arr::get($listing, 'meta', []),
            'rowFilters' => Arr::get($listing, 'filters', []),
            'tableColumns' => $this->service->tableColumns($entity),
            'fields' => $this->service->fieldDefinitions($entity),
            'editRow' => $editRow,
            'confessions' => Confession::query()->orderBy('id')->get(['id', 'name']),
            'commandLinks' => ReligionCommand::query()
                ->with('confession:id,name')
                ->orderByDesc('id')
                ->limit(50)
                ->get(),
            'reminderTypes' => ReminderType::query()->orderBy('name')->get(['id', 'name']),
            'implementations' => ReminderImplementation::query()->orderBy('implementation_key')->get(['id', 'implementation_key']),
            'rituals' => Ritual::query()->orderBy('name')->get(['id', 'name', 'ritual_key', 'description']),
            'sampleBundleText' => $sampleBundleText,
            'userSelections' => $userSelections,
            'wordModeReminders' => $wordModeReminders,
        ]);
    }

    private function buildUserLabel(string $username, string $displayName, string $externalUserId): string
    {
        $username = trim($username);
        if ($username !== '') {
            return '@'.$username;
        }

        $displayName = trim($displayName);
        if ($displayName !== '') {
            return $displayName;
        }

        return $externalUserId !== '' ? 'user:'.$externalUserId : 'user';
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'entity' => ['required', 'string'],
            'payload_json' => ['required', 'string'],
        ]);

        try {
            $payload = json_decode((string) $data['payload_json'], true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($payload)) {
                throw new \RuntimeException('Payload must decode to a JSON object.');
            }

            $this->service->create((string) $data['entity'], $payload);

            return redirect()->route('religions.admin', ['entity' => $data['entity']])
                ->with('status', 'Created successfully.');
        } catch (Throwable $exception) {
            return redirect()->route('religions.admin', ['entity' => $data['entity']])
                ->withErrors(['crud_create' => $exception->getMessage()]);
        }
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'entity' => ['required', 'string'],
            'id' => ['required', 'integer', 'min:1'],
            'payload_json' => ['required', 'string'],
        ]);

        try {
            $payload = json_decode((string) $data['payload_json'], true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($payload)) {
                throw new \RuntimeException('Payload must decode to a JSON object.');
            }

            $this->service->update((string) $data['entity'], (int) $data['id'], $payload);

            return redirect()->route('religions.admin', ['entity' => $data['entity']])
                ->with('status', 'Updated successfully.');
        } catch (Throwable $exception) {
            return redirect()->route('religions.admin', ['entity' => $data['entity']])
                ->withErrors(['crud_update' => $exception->getMessage()]);
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'entity' => ['required', 'string'],
            'id' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $this->service->delete((string) $data['entity'], (int) $data['id']);

            return redirect()->route('religions.admin', ['entity' => $data['entity']])
                ->with('status', 'Deleted successfully.');
        } catch (Throwable $exception) {
            return redirect()->route('religions.admin', ['entity' => $data['entity']])
                ->withErrors(['crud_delete' => $exception->getMessage()]);
        }
    }
}
