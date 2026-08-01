<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Religions Admin</title>
    <style>
        :root {
            --bg: #f4f6fa;
            --card: #ffffff;
            --line: #dbe2ea;
            --text: #0f172a;
            --muted: #475569;
            --primary: #0b5ed7;
            --primary-soft: #e6f0ff;
            --danger: #b42318;
            --ok-bg: #ecfdf3;
            --ok-text: #067647;
            --err-bg: #fef3f2;
            --err-text: #b42318;
        }

        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, sans-serif;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            color: var(--text);
        }

        .layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            border-right: 1px solid var(--line);
            background: #0f172a;
            color: #dbeafe;
            padding: 16px;
        }

        .sidebar h2 {
            font-size: 16px;
            margin: 0 0 12px;
            color: #f8fafc;
        }

        .sidebar .group {
            margin-bottom: 14px;
        }

        .sidebar a {
            display: block;
            color: #bfdbfe;
            text-decoration: none;
            padding: 8px 10px;
            border-radius: 8px;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .sidebar a:hover {
            background: #1e293b;
        }

        .sidebar a.active {
            background: #1d4ed8;
            color: #ffffff;
            font-weight: 600;
        }

        .main {
            padding: 20px;
        }

        .header {
            margin-bottom: 14px;
        }

        .header h1 {
            margin: 0 0 4px;
            font-size: 24px;
        }

        .header p {
            margin: 0;
            color: var(--muted);
        }

        .top-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .card h2 {
            margin: 0 0 12px;
            font-size: 18px;
        }

        .grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        label {
            display: block;
            margin: 0 0 6px;
            font-weight: 600;
            font-size: 13px;
        }

        input, select, textarea {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 9px 10px;
            font-size: 14px;
        }

        textarea {
            min-height: 96px;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        button {
            border: 0;
            border-radius: 8px;
            padding: 9px 12px;
            font-weight: 600;
            cursor: pointer;
            color: #fff;
            background: var(--primary);
        }

        button.secondary {
            background: #334155;
        }

        .btn-link {
            display: inline-block;
            border-radius: 8px;
            padding: 9px 12px;
            font-weight: 600;
            text-decoration: none;
            color: #fff;
            background: #334155;
        }

        button.danger {
            background: var(--danger);
        }

        .status {
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .status.ok {
            background: var(--ok-bg);
            color: var(--ok-text);
            border: 1px solid #a6f4c5;
        }

        .status.err {
            background: var(--err-bg);
            color: var(--err-text);
            border: 1px solid #fecdd3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td {
            border: 1px solid var(--line);
            padding: 8px;
            vertical-align: top;
            text-align: left;
        }

        td pre {
            margin: 0;
            white-space: pre-wrap;
        }

        .small {
            font-size: 12px;
            color: var(--muted);
        }

        .table-tools {
            display: grid;
            grid-template-columns: 1fr 180px 140px auto;
            gap: 8px;
            align-items: end;
            margin-bottom: 12px;
        }

        .pager {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pager-links {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .cell-null {
            color: #94a3b8;
        }

        .cell-pre {
            margin: 0;
            white-space: pre-wrap;
            max-height: 140px;
            overflow: auto;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 12px;
        }

        .sort-link {
            color: #0f172a;
            text-decoration: none;
            font-weight: 700;
        }

        .sort-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 960px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                border-right: 0;
                border-bottom: 1px solid #1e293b;
            }

            .table-tools {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <h2>Religions Admin</h2>
        <div class="group">
            <a href="{{ route('religions.admin') }}" class="{{ request()->routeIs('religions.admin') ? 'active' : '' }}">Overview</a>
            @foreach ($entities as $navEntity)
                <a href="{{ route('religions.admin.entity', ['entity' => $navEntity]) }}" class="{{ $entity === $navEntity ? 'active' : '' }}">{{ $entityLabels[$navEntity] ?? $navEntity }}</a>
            @endforeach
        </div>
    </aside>

    <main class="main">
        <div class="header">
            <h1>{{ $entityLabels[$entity] ?? $entity }}</h1>
            <p>
                @if ($mode === 'list')
                    Listing page for this entity.
                @elseif ($mode === 'create')
                    Create page for this entity.
                @elseif ($mode === 'edit')
                    Edit page for selected row.
                @elseif ($mode === 'link-command')
                    Dedicated command mapping page.
                @endif
            </p>
            <div class="top-actions">
                <a class="btn-link" href="{{ route('religions.admin.entity', ['entity' => $entity]) }}">List</a>
                <a class="btn-link" href="{{ route('religions.admin.create', ['entity' => $entity]) }}">Create</a>
                @if ($entity === 'religion_commands')
                    <a class="btn-link" href="{{ route('religions.admin.command-link') }}">Link Command</a>
                    <a class="btn-link" href="{{ route('religions.admin.bundle-export', ['confession_id' => 2]) }}">Export Bundle TXT</a>
                    <a class="btn-link" href="{{ route('religions.admin.command-link') }}#bundle-tools">Import Bundle TXT</a>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="status ok">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="status err">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($mode === 'link-command' && $entity === 'religion_commands')
            <section class="card">
                <h2>Quick Link Command</h2>
                <p class="small">Short path to map internal command key to Telegram trigger.</p>
                <form method="post" action="{{ route('religions.admin.link-command') }}">
                    @csrf
                    <div class="grid">
                        <div>
                            <label for="quick_confession_id">Confession</label>
                            <select id="quick_confession_id" name="confession_id" required>
                                @foreach ($confessions as $confession)
                                    <option value="{{ $confession->id }}">#{{ $confession->id }} {{ $confession->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="quick_command_key">Command Key</label>
                            <input id="quick_command_key" name="command_key" placeholder="weekly-chapter" required>
                        </div>
                        <div>
                            <label for="quick_tg_command">Telegram Trigger</label>
                            <input id="quick_tg_command" name="tg_command" placeholder="/chapter" required>
                        </div>
                        <div>
                            <label for="quick_name">Name</label>
                            <input id="quick_name" name="name" placeholder="Weekly Chapter">
                        </div>
                        <div>
                            <label for="quick_description">Description</label>
                            <input id="quick_description" name="description" placeholder="Command mapping">
                        </div>
                        <div>
                            <label for="quick_is_active">Active</label>
                            <select id="quick_is_active" name="is_active">
                                <option value="1" selected>Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="submit">Save Mapping</button>
                    </div>
                </form>
            </section>

            <section class="card">
                <h2>Reminder Setup Wizard</h2>
                <p class="small">Create command mapping and reminder in one submit. If ritual is selected, reminder text is taken from ritual description.</p>
                <form method="post" action="{{ route('religions.admin.reminder-wizard') }}">
                    @csrf
                    <div class="grid">
                        <div>
                            <label for="wizard_confession_id">Confession</label>
                            <select id="wizard_confession_id" name="confession_id" required>
                                @foreach ($confessions as $confession)
                                    <option value="{{ $confession->id }}">#{{ $confession->id }} {{ $confession->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="wizard_command_key">Command Key</label>
                            <input id="wizard_command_key" name="command_key" placeholder="morning-note" required>
                        </div>
                        <div>
                            <label for="wizard_tg_command">Telegram Command</label>
                            <input id="wizard_tg_command" name="tg_command" placeholder="/morningNote" required>
                        </div>
                        <div>
                            <label for="wizard_command_name">Command Name</label>
                            <input id="wizard_command_name" name="command_name" placeholder="Morning Note">
                        </div>
                        <div>
                            <label for="wizard_command_description">Command Description</label>
                            <input id="wizard_command_description" name="command_description" placeholder="Sends daily morning note">
                        </div>
                        <div>
                            <label for="wizard_reminder_title">Reminder Title</label>
                            <input id="wizard_reminder_title" name="reminder_title" placeholder="Morning Note" required>
                        </div>
                        <div>
                            <label for="wizard_reminder_type_id">Reminder Type</label>
                            <select id="wizard_reminder_type_id" name="reminder_type_id" required>
                                @foreach ($reminderTypes as $type)
                                    <option value="{{ $type->id }}">#{{ $type->id }} {{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="wizard_implementation_id">Implementation (optional)</label>
                            <select id="wizard_implementation_id" name="implementation_id">
                                <option value="">-- none --</option>
                                @foreach ($implementations as $implementation)
                                    <option value="{{ $implementation->id }}">#{{ $implementation->id }} {{ $implementation->implementation_key }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="wizard_ritual_id">Ritual (optional)</label>
                            <select id="wizard_ritual_id" name="ritual_id">
                                <option value="">-- none --</option>
                                @foreach ($rituals as $ritual)
                                    <option value="{{ $ritual->id }}" data-description="{{ $ritual->description ?? '' }}">#{{ $ritual->id }} {{ $ritual->name }} ({{ $ritual->ritual_key }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="wizard_text_format">Text Format</label>
                            <select id="wizard_text_format" name="text_format" required>
                                <option value="plain" selected>plain</option>
                                <option value="markdown">markdown</option>
                                <option value="wysiwyg">wysiwyg</option>
                            </select>
                        </div>
                        <div>
                            <label for="wizard_schedule_preset">Schedule Preset</label>
                            <select id="wizard_schedule_preset" name="schedule_preset" required>
                                <option value="command" selected>command (manual trigger)</option>
                                <option value="interval">interval (every N minutes)</option>
                                <option value="daily">daily</option>
                                <option value="weekly">weekly</option>
                                <option value="monthly">monthly</option>
                                <option value="yearly">yearly</option>
                            </select>
                        </div>
                        <div>
                            <label for="wizard_interval_minutes">Interval Minutes</label>
                            <input id="wizard_interval_minutes" type="number" name="interval_minutes" min="1" max="1440" value="60">
                        </div>
                        <div>
                            <label for="wizard_schedule_time">Time (HH:MM)</label>
                            <input id="wizard_schedule_time" type="time" name="schedule_time" value="08:00">
                        </div>
                        <div>
                            <label for="wizard_schedule_timezone">Timezone</label>
                            <select id="wizard_schedule_timezone" name="schedule_timezone">
                                <option value="Europe/Kyiv" selected>Europe/Kyiv</option>
                                @foreach (DateTimeZone::listIdentifiers() as $timezone)
                                    @if ($timezone !== 'Europe/Kyiv')
                                        <option value="{{ $timezone }}">{{ $timezone }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="wizard_weekly_day">Weekly Day</label>
                            <select id="wizard_weekly_day" name="weekly_day">
                                <option value="mon" selected>Monday</option>
                                <option value="tue">Tuesday</option>
                                <option value="wed">Wednesday</option>
                                <option value="thu">Thursday</option>
                                <option value="fri">Friday</option>
                                <option value="sat">Saturday</option>
                                <option value="sun">Sunday</option>
                            </select>
                        </div>
                        <div>
                            <label for="wizard_monthly_day">Monthly Day</label>
                            <input id="wizard_monthly_day" type="number" name="monthly_day" min="1" max="31" value="1">
                        </div>
                        <div>
                            <label for="wizard_yearly_month">Yearly Month</label>
                            <input id="wizard_yearly_month" type="number" name="yearly_month" min="1" max="12" value="1">
                        </div>
                        <div>
                            <label for="wizard_yearly_day">Yearly Day</label>
                            <input id="wizard_yearly_day" type="number" name="yearly_day" min="1" max="31" value="1">
                        </div>
                        <div>
                            <label for="wizard_is_active">Active</label>
                            <select id="wizard_is_active" name="is_active">
                                <option value="1" selected>Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label for="wizard_reminder_text">Reminder Text</label>
                            <textarea id="wizard_reminder_text" name="reminder_text" placeholder="Auto-filled from selected ritual description."></textarea>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="submit">Create Command + Reminder</button>
                    </div>
                </form>
            </section>

            <section class="card">
                <h2>Recent Command Links</h2>
                @if ($commandLinks->isEmpty())
                    <p>No command links yet.</p>
                @else
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Confession</th>
                            <th>Command Key</th>
                            <th>Trigger</th>
                            <th>Name</th>
                            <th>Active</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($commandLinks as $link)
                            <tr>
                                <td>{{ $link->id }}</td>
                                <td>{{ $link->confession?->name ?? '-' }}</td>
                                <td>{{ $link->command_key }}</td>
                                <td>{{ $link->trigger }}</td>
                                <td>{{ $link->name }}</td>
                                <td>{{ $link->is_active ? 'Yes' : 'No' }}</td>
                                <td>
                                    <a class="btn-link" href="{{ route('religions.admin.edit', ['entity' => 'religion_commands', 'id' => $link->id]) }}">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </section>

            <section class="card">
                <h2>User Religion & Confession Selections</h2>
                @if (empty($userSelections))
                    <p>No active user selections yet.</p>
                @else
                    <table>
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Group</th>
                            <th>Religions</th>
                            <th>Confessions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($userSelections as $entry)
                            <tr>
                                <td>{{ $entry['user_label'] }}</td>
                                <td>{{ $entry['group_label'] }}</td>
                                <td>{{ $entry['religions'] }}</td>
                                <td>{{ $entry['confessions'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </section>

            <section class="card">
                <h2>Word Of The Day Smoke Test</h2>
                <p class="small">Sets a selected active <code>word_mode</code> reminder to the next minute in its own timezone and clears dispatch markers for a quick runtime check.</p>
                @if (empty($wordModeReminders) || $wordModeReminders->isEmpty())
                    <p>No active word_mode reminders found.</p>
                @else
                    <form method="post" action="{{ route('religions.admin.word-mode-next-minute') }}">
                        @csrf
                        <div class="grid">
                            <div>
                                <label for="word_mode_smoke_reminder_id">Word-mode Reminder</label>
                                <select id="word_mode_smoke_reminder_id" name="reminder_id" required>
                                    @foreach ($wordModeReminders as $reminder)
                                        <option value="{{ $reminder->id }}">
                                            #{{ $reminder->id }} {{ $reminder->title }} | {{ $reminder->confession?->name ?? 'confession' }} | {{ $reminder->schedule_time ?? '--:--' }} {{ $reminder->schedule_timezone ?? 'Europe/Kyiv' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="actions">
                            <button type="submit">Set Word-Of-Day Reminder To Next Minute</button>
                        </div>
                    </form>
                @endif
            </section>

            <section class="card" id="bundle-tools">
                <h2>Bundle Import / Export (TXT)</h2>
                <p class="small">Exports and imports religion + confession + rituals + reminders + languages using sectioned text blocks. Sample is based on confession #2 with English language defaults.</p>

                <form method="get" action="{{ route('religions.admin.bundle-export') }}">
                    <div class="grid">
                        <div>
                            <label for="bundle_confession_id">Confession for Export</label>
                            <select id="bundle_confession_id" name="confession_id">
                                @foreach ($confessions as $confession)
                                    <option value="{{ $confession->id }}" {{ (int) request('confession_id', 2) === (int) $confession->id ? 'selected' : '' }}>
                                        #{{ $confession->id }} {{ $confession->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="submit">Export TXT</button>
                    </div>
                </form>

                <form method="post" action="{{ route('religions.admin.bundle-import') }}" style="margin-top: 12px;">
                    @csrf
                    <div>
                        <label for="bundle_text">Sectioned TXT Bundle</label>
                        <textarea id="bundle_text" name="bundle_text" rows="20" placeholder="Paste sectioned bundle text here.">{{ old('bundle_text', $sampleBundleText ?? '') }}</textarea>
                    </div>
                    <div class="actions">
                        <button type="submit">Import Bundle</button>
                    </div>
                </form>
            </section>
        @endif

        @if ($mode === 'create')
            <section class="card">
                <h2>Create {{ $entityLabels[$entity] ?? $entity }}</h2>
                <form method="post" action="{{ route('religions.admin.entity.store', ['entity' => $entity]) }}">
                    @csrf
                    <div class="grid">
                        @foreach ($fields as $field)
                            <div data-field-name="{{ $field['name'] }}">
                                <label for="create_{{ $field['name'] }}">{{ $field['label'] }}</label>
                                @if ($field['type'] === 'textarea' || $field['type'] === 'json')
                                    <textarea id="create_{{ $field['name'] }}" name="{{ $field['name'] }}" @if($field['required']) required @endif placeholder="{{ $field['type'] === 'json' ? '{}' : '' }}">{{ old($field['name']) }}</textarea>
                                @elseif ($field['type'] === 'select')
                                    <select id="create_{{ $field['name'] }}" name="{{ $field['name'] }}" @if($field['required']) required @endif>
                                        @if ($field['nullable'])
                                            <option value="">-- none --</option>
                                        @endif
                                        @foreach ($field['options'] as $option)
                                            <option value="{{ $option['value'] }}" {{ old($field['name'], $field['name'] === 'schedule_timezone' ? 'Europe/Kyiv' : null) == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($field['type'] === 'boolean')
                                    <select id="create_{{ $field['name'] }}" name="{{ $field['name'] }}">
                                        <option value="1" {{ old($field['name'], '1') === '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old($field['name']) === '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                @elseif ($field['type'] === 'datetime')
                                    <input id="create_{{ $field['name'] }}" type="datetime-local" name="{{ $field['name'] }}" value="{{ old($field['name']) }}" @if($field['required']) required @endif>
                                @elseif ($field['type'] === 'time')
                                    <input id="create_{{ $field['name'] }}" type="time" name="{{ $field['name'] }}" value="{{ old($field['name']) }}" @if($field['required']) required @endif>
                                @elseif ($field['type'] === 'number')
                                    <input id="create_{{ $field['name'] }}" type="number" name="{{ $field['name'] }}" value="{{ old($field['name']) }}" @if($field['required']) required @endif>
                                @else
                                    <input id="create_{{ $field['name'] }}" type="text" name="{{ $field['name'] }}" value="{{ old($field['name']) }}" @if($field['required']) required @endif>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="actions">
                        <button type="submit">Create</button>
                        <a class="btn-link" href="{{ route('religions.admin.entity', ['entity' => $entity]) }}">Back to List</a>
                    </div>
                </form>
            </section>
        @endif

        @if ($mode === 'edit' && !empty($editRow))
            <section class="card">
                <h2>Edit #{{ $editRow['id'] }}</h2>
                <form method="post" action="{{ route('religions.admin.entity.update', ['entity' => $entity, 'id' => $editRow['id']]) }}">
                    @csrf
                    <div class="grid">
                        @foreach ($fields as $field)
                            @php
                                $existing = $editRow[$field['name']] ?? null;
                                if ($field['type'] === 'json' && is_array($existing)) {
                                    $existing = json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }
                                if ($field['type'] === 'datetime' && is_string($existing)) {
                                    $existing = str_replace(' ', 'T', substr($existing, 0, 16));
                                }
                                if ($field['type'] === 'time' && is_string($existing)) {
                                    $existing = substr($existing, 0, 5);
                                }
                            @endphp
                            <div data-field-name="{{ $field['name'] }}">
                                <label for="edit_{{ $field['name'] }}">{{ $field['label'] }}</label>
                                @if ($field['type'] === 'textarea' || $field['type'] === 'json')
                                    <textarea id="edit_{{ $field['name'] }}" name="{{ $field['name'] }}" @if($field['required']) required @endif>{{ old($field['name'], (string) $existing) }}</textarea>
                                @elseif ($field['type'] === 'select')
                                    <select id="edit_{{ $field['name'] }}" name="{{ $field['name'] }}" @if($field['required']) required @endif>
                                        @if ($field['nullable'])
                                            <option value="">-- none --</option>
                                        @endif
                                        @foreach ($field['options'] as $option)
                                            <option value="{{ $option['value'] }}" {{ (string) old($field['name'], $field['name'] === 'schedule_timezone' && (string) $existing === '' ? 'Europe/Kyiv' : (string) $existing) === (string) $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($field['type'] === 'boolean')
                                    <select id="edit_{{ $field['name'] }}" name="{{ $field['name'] }}">
                                        <option value="1" {{ (string) old($field['name'], $existing ? '1' : '0') === '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ (string) old($field['name'], $existing ? '1' : '0') === '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                @elseif ($field['type'] === 'datetime')
                                    <input id="edit_{{ $field['name'] }}" type="datetime-local" name="{{ $field['name'] }}" value="{{ old($field['name'], (string) $existing) }}" @if($field['required']) required @endif>
                                @elseif ($field['type'] === 'time')
                                    <input id="edit_{{ $field['name'] }}" type="time" name="{{ $field['name'] }}" value="{{ old($field['name'], (string) $existing) }}" @if($field['required']) required @endif>
                                @elseif ($field['type'] === 'number')
                                    <input id="edit_{{ $field['name'] }}" type="number" name="{{ $field['name'] }}" value="{{ old($field['name'], (string) $existing) }}" @if($field['required']) required @endif>
                                @else
                                    <input id="edit_{{ $field['name'] }}" type="text" name="{{ $field['name'] }}" value="{{ old($field['name'], (string) $existing) }}" @if($field['required']) required @endif>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="actions">
                        <button type="submit">Save Changes</button>
                        <a class="btn-link" href="{{ route('religions.admin.entity', ['entity' => $entity]) }}">Cancel</a>
                    </div>
                </form>
            </section>
        @endif

        @if ($mode === 'list')
        <section class="card">
            <h2>Rows</h2>

            <form method="get" action="{{ route('religions.admin.entity', ['entity' => $entity]) }}" class="table-tools">
                <div>
                    <label for="q">Search</label>
                    <input id="q" name="q" value="{{ $rowFilters['q'] ?? '' }}" placeholder="Search by key fields">
                </div>
                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="all" {{ ($rowFilters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All</option>
                        <option value="active" {{ ($rowFilters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ ($rowFilters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <label for="per_page">Rows</label>
                    <select id="per_page" name="per_page">
                        @foreach ([10, 20, 50, 100] as $size)
                            <option value="{{ $size }}" {{ (int) ($rowFilters['per_page'] ?? 20) === $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="actions" style="margin:0;">
                    <button type="submit">Apply</button>
                    <a class="btn-link" href="{{ route('religions.admin.entity', ['entity' => $entity]) }}">Reset</a>
                    <a class="btn-link" href="{{ route('religions.admin.create', ['entity' => $entity]) }}">Create New</a>
                </div>
            </form>

            @if (empty($rows))
                <p>No rows yet.</p>
            @else
                <table>
                    <thead>
                    <tr>
                        @foreach ($tableColumns as $column)
                            <th>
                                @if ($column['sortable'])
                                    @php
                                        $currentSort = (string) ($rowFilters['sort'] ?? 'id');
                                        $currentDir = (string) ($rowFilters['dir'] ?? 'desc');
                                        $isCurrentSort = $currentSort === $column['name'];
                                        $nextDir = $isCurrentSort && $currentDir === 'asc' ? 'desc' : 'asc';
                                        $sortIndicator = $isCurrentSort ? ($currentDir === 'asc' ? ' ↑' : ' ↓') : '';
                                        $sortParams = [
                                            'entity' => $entity,
                                            'q' => $rowFilters['q'] ?? null,
                                            'status' => $rowFilters['status'] ?? 'all',
                                            'per_page' => $rowFilters['per_page'] ?? 20,
                                            'sort' => $column['name'],
                                            'dir' => $nextDir,
                                        ];
                                    @endphp
                                    <a class="sort-link" href="{{ route('religions.admin.entity', $sortParams) }}">{{ $column['label'] }}{{ $sortIndicator }}</a>
                                @else
                                    {{ $column['label'] }}
                                @endif
                            </th>
                        @endforeach
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            @foreach ($tableColumns as $column)
                                @php
                                    $key = $column['name'];
                                    $value = $row[$key] ?? null;
                                @endphp
                                <td>
                                    @if (is_bool($value))
                                        {{ $value ? 'Yes' : 'No' }}
                                    @elseif (is_array($value))
                                        <pre class="cell-pre">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @elseif ($value === null || $value === '')
                                        <span class="cell-null">-</span>
                                    @else
                                        {{ (string) $value }}
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                <div class="actions">
                                    <a class="btn-link" href="{{ route('religions.admin.edit', ['entity' => $entity, 'id' => $row['id']]) }}">Edit</a>
                                    <form method="post" action="{{ route('religions.admin.entity.destroy', ['entity' => $entity, 'id' => $row['id']]) }}" onsubmit="return confirm('Delete this row?');">
                                        @csrf
                                        <button type="submit" class="danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="pager">
                    <div class="small">
                        Showing {{ $rowsMeta['from'] ?? 0 }}-{{ $rowsMeta['to'] ?? 0 }} of {{ $rowsMeta['total'] ?? 0 }}
                    </div>
                    <div class="pager-links">
                        @php
                            $currentPage = (int) ($rowsMeta['current_page'] ?? 1);
                            $lastPage = (int) ($rowsMeta['last_page'] ?? 1);
                            $base = [
                                'entity' => $entity,
                                'q' => $rowFilters['q'] ?? null,
                                'status' => $rowFilters['status'] ?? 'all',
                                'per_page' => $rowFilters['per_page'] ?? 20,
                                'sort' => $rowFilters['sort'] ?? 'id',
                                'dir' => $rowFilters['dir'] ?? 'desc',
                            ];
                        @endphp
                        @if ($currentPage > 1)
                            <a class="btn-link" href="{{ route('religions.admin.entity', array_merge($base, ['page' => $currentPage - 1])) }}">Prev</a>
                        @endif
                        <span class="small">Page {{ $currentPage }} / {{ $lastPage }}</span>
                        @if ($currentPage < $lastPage)
                            <a class="btn-link" href="{{ route('religions.admin.entity', array_merge($base, ['page' => $currentPage + 1])) }}">Next</a>
                        @endif
                    </div>
                </div>
            @endif
        </section>
        @endif
    </main>
</div>
<script>
    (function () {
        const ritualSelect = document.getElementById('wizard_ritual_id');
        const reminderText = document.getElementById('wizard_reminder_text');
        const schedulePreset = document.getElementById('wizard_schedule_preset');
        const intervalMinutes = document.getElementById('wizard_interval_minutes');
        const scheduleTime = document.getElementById('wizard_schedule_time');
        const weeklyDay = document.getElementById('wizard_weekly_day');
        const monthlyDay = document.getElementById('wizard_monthly_day');
        const yearlyMonth = document.getElementById('wizard_yearly_month');
        const yearlyDay = document.getElementById('wizard_yearly_day');

        if (ritualSelect && reminderText) {
            const syncReminderText = () => {
                const option = ritualSelect.options[ritualSelect.selectedIndex];

                if (!option || option.value === '') {
                    return;
                }

                reminderText.value = option.getAttribute('data-description') || '';
            };

            ritualSelect.addEventListener('change', syncReminderText);
            syncReminderText();
        }

        if (schedulePreset && intervalMinutes && scheduleTime && weeklyDay && monthlyDay && yearlyMonth && yearlyDay) {
            const toggleScheduleInputs = () => {
                const preset = schedulePreset.value;
                const isCommand = preset === 'command';
                const isInterval = preset === 'interval';

                intervalMinutes.disabled = !isInterval;
                scheduleTime.disabled = isCommand || isInterval;
                weeklyDay.disabled = preset !== 'weekly';
                monthlyDay.disabled = preset !== 'monthly';
                yearlyMonth.disabled = preset !== 'yearly';
                yearlyDay.disabled = preset !== 'yearly';
            };

            schedulePreset.addEventListener('change', toggleScheduleInputs);
            toggleScheduleInputs();
        }
    })();

    (function () {
        const hasReminderScheduleInputs = document.getElementById('create_schedule_preset') || document.getElementById('edit_schedule_preset');

        if (!hasReminderScheduleInputs) {
            return;
        }

        const applySchedulePresetDependencies = (prefix) => {
            const presetSelect = document.getElementById(prefix + 'schedule_preset');
            if (!presetSelect) {
                return;
            }

            const form = presetSelect.closest('form');
            if (!form) {
                return;
            }

            const fields = {
                interval_minutes: form.querySelector('[data-field-name="interval_minutes"]'),
                schedule_time: form.querySelector('[data-field-name="schedule_time"]'),
                schedule_weekday: form.querySelector('[data-field-name="schedule_weekday"]'),
                schedule_monthday: form.querySelector('[data-field-name="schedule_monthday"]'),
                schedule_year_month: form.querySelector('[data-field-name="schedule_year_month"]'),
                schedule_year_day: form.querySelector('[data-field-name="schedule_year_day"]'),
                frequency_value: form.querySelector('[data-field-name="frequency_value"]'),
            };

            const setVisible = (fieldKey, visible) => {
                const wrapper = fields[fieldKey];
                if (!wrapper) {
                    return;
                }

                wrapper.style.display = visible ? '' : 'none';

                const input = wrapper.querySelector('input, select, textarea');
                if (!input) {
                    return;
                }

                if (!visible) {
                    input.disabled = true;
                } else {
                    input.disabled = false;
                }
            };

            const update = () => {
                const preset = presetSelect.value;

                setVisible('interval_minutes', preset === 'interval');
                setVisible('schedule_time', ['daily', 'weekly', 'monthly', 'yearly'].includes(preset));
                setVisible('schedule_weekday', preset === 'weekly');
                setVisible('schedule_monthday', preset === 'monthly');
                setVisible('schedule_year_month', preset === 'yearly');
                setVisible('schedule_year_day', preset === 'yearly');
                setVisible('frequency_value', preset === 'custom_cron');
            };

            presetSelect.addEventListener('change', update);
            update();
        };

        applySchedulePresetDependencies('create_');
        applySchedulePresetDependencies('edit_');
    })();
</script>
</body>
</html>
