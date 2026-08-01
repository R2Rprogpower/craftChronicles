# Implemented Features and Access

This file summarizes what is implemented and where to access it.

## Core Platform

- Laravel 12 modular structure under app/Modules
- Module API route loading from routes/api.php
- Auth/permissions modules from craftChronicles baseline
- Messenger abstraction with Telegram driver

## Telegram Integration

- Bot setup admin UI and flow
- Telegram API client
- Webhook ingestion endpoint
- Update persistence to messenger_updates
- Group auto-linking for group and supergroup updates

Access:
- Bot setup page: /bots/setup
- Webhook endpoint: /api/telegram/webhook/{botId}

## Religion and Reminder Data Model

Implemented tables and models:
- religions
- confessions
- rituals
- reminder_types
- reminder_implementations
- religion_commands
- religion_reminders
- language_packs
- language_words
- user_group_religion_preferences
- reminder_delivery_targets
- reminder_runs

## Admin CRUD

- Sidebar CRUD admin for Religions module entities
- Structured per-field forms for create and update (no JSON-only workflow)
- Per-row actions for edit and delete
- Entity-aware forms with FK select options and boolean/datetime handling

Access:
- Religions admin CRUD page: /admin/religions
- Entity pages: /admin/religions/{entity}
- Edit pages: /admin/religions/{entity}/{id}/edit

Key reminder/language admin updates:

- `religion_reminders.schedule_time` uses a time field.
- `religion_reminders.schedule_timezone` is selectable (Kyiv default).
- `language_words` is a first-class CRUD entity with `word`, `translation`, `transcription`, `phrases`, metadata, and activation flag.
- Reminder listing includes timezone visibility.

## Custom Command Mapping UI

- Dedicated admin form to map:
  - internal command key
  - Telegram trigger command
  - confession scope
- Saves to religion_commands with upsert behavior
- Available in religion commands entity page with quick-link form

Access:
- /admin/religions/religion_commands section: Quick Link Command
- /admin/religions/commands/link page: command mapping + reminder wizard + recent links + user selections + bundle tools

Bundle import/export tooling implemented on command-link page:

- Export/import TXT sections: `[RELIGION] [CONFESSION] [LANGUAGES] [RITUALS] [REMINDERS] [WORDS]`
- Supports words with `phrases` and backward compatibility with legacy `LANGUAGES.words_json`
- Includes default English sample words merged with DB data for confession #2 sample export

## Runtime Command Dispatch

Implemented command execution pipeline:

1. Parse slash command from incoming Telegram message
2. Resolve MessengerUser from Telegram sender
3. Resolve MessengerGroupLink for current bot and chat
4. Resolve user confession via user_group_religion_preferences
5. Resolve religion_commands by confession + trigger
6. Resolve handler class:
- first from reminder_implementations.handler_class
- fallback from config/religion_command_handlers.php
- final fallback to echo reminder content handler
7. Execute handler and send reply to Telegram chat

Additional runtime behavior implemented:

- Built-in command suite: `/help`, `/aboutme`, `/follower`, `/religionList`, `/religionChoose_*`, `/religionDescribe_*`, `/confessionDescribe_*`, `/confessionLeave_*`, `/ritualList`
- Multi-confession membership per user/group (join multiple, leave individually)
- Help output includes selected religions and selected confessions in current group context
- `/follower` lists active followers in the current group grouped by confession, with optional confession slug filter
- Reminder/user mention style standardized to: `Dear @username1, @username2`

New classes:
- app/Modules/Telegram/Services/TelegramCommandDispatchService.php
- app/Modules/Telegram/DTO/TelegramCommandContext.php
- app/Modules/Telegram/Contracts/TelegramCommandHandlerInterface.php
- app/Modules/Telegram/Services/Handlers/EchoReminderContentHandler.php
- app/Modules/Reminders/Services/WeeklyChapterResolver.php
- app/Modules/Reminders/Services/HolidayCalendarResolver.php
- app/Modules/Reminders/Services/RitualScheduleResolver.php
- config/religion_command_handlers.php

Reminder scheduler/runtime updates:

- Timezone-aware due checking with catch-up behavior based on previous run metadata
- Word-mode reminder content generated from random active `language_words` entry first
- Fallback word source from `language_packs.meta_json.words` for legacy records
- Word-mode per-group once-per-day delivery guard using reminder `meta_json.word_mode_last_sent_by_group`

## Seed Data

Seeder provides:
- 1 religion and 1 confession
- reminder types and implementations (manual and auto)
- rituals
- command mappings (/command1, /command2)
- reminders and runtime bindings

Seeder class:
- App\\Modules\\Religions\\Database\\Seeders\\ReligionsSeeder

Sample/export words coverage:

- Seed/sample flows now include richer word records and phrase examples for import/export tests

## Coherence Status

Current status is coherent for implemented scope:

- Messenger and Religions models align with their migrations and table names
- Foreign keys and unique constraints reflect model relations used in CRUD and runtime dispatch
- Seeded handler class names now resolve to existing classes
- Command mapping UI and runtime command dispatch use the same religion_commands table

Known scope limits (not broken, but not complete to full long-term roadmap):
- Advanced module split (Rituals/Reminders/Content/Languages/Learning/Admin as full isolated modules) is still partial
- Full policy/MFA gating and audit/revision workflow for all admin writes is not yet fully wired
- Queue-based planner/executor decomposition remains partial; current reminder dispatch is stable via scheduler + polling runtime workflow
