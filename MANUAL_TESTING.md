# Manual Testing Guide

This guide covers manual verification for all currently implemented features.

## Preconditions

1. Project root is this directory.
2. Dependencies are installed.
3. Database and Redis are running.
4. You have a Telegram bot token and a test group.

## URL Map (Local)

Base URL (nginx):

- http://localhost:8080

Main pages:

1. Login page: http://localhost:8080/login
2. Dashboard (after login): http://localhost:8080/dashboard
3. Bot setup page: http://localhost:8080/bots/setup
4. Religions admin page: http://localhost:8080/admin/religions
5. Religions commands entity: http://localhost:8080/admin/religions/religion_commands
6. Religions entity example: http://localhost:8080/admin/religions/religions
7. Religions create page example: http://localhost:8080/admin/religions/religions/create
8. Command-link page: http://localhost:8080/admin/religions/commands/link
9. Filtered entity example: http://localhost:8080/admin/religions/religion_commands?q=chapter&status=active&per_page=10
10. Paginated entity example: http://localhost:8080/admin/religions/religion_commands?page=2&per_page=10
11. Sorted by name asc example: http://localhost:8080/admin/religions/religion_commands?sort=name&dir=asc
12. Sorted by updated_at desc example: http://localhost:8080/admin/religions/religion_commands?sort=updated_at&dir=desc

Main API endpoints:

1. Signup API: POST http://localhost:8080/api/auth/signup
2. Login API: POST http://localhost:8080/api/auth/login
3. Telegram webhook: POST http://localhost:8080/api/telegram/webhook/{botId}

## A) Bootstrap and Environment

1. Copy env:

```bash
cp .env.example .env
```

2. Start services:

```bash
docker compose up -d --build
```

3. Install dependencies (container preferred):

```bash
docker compose run --rm app composer install
```

4. Generate app key:

```bash
docker compose run --rm app php artisan key:generate
```

5. Run migrations and seeder:

```bash
docker compose run --rm app php artisan migrate
docker compose run --rm app php artisan db:seed --class="App\\Modules\\Religions\\Database\\Seeders\\ReligionsSeeder"
```

## B) Route Availability

Run:

```bash
docker compose run --rm app php artisan route:list
```

Check these routes exist:

1. /api/telegram/webhook/{botId}
2. /bots/setup
3. /admin/religions
4. /admin/religions/{entity}
5. /admin/religions/{entity}/{id}/edit
6. /admin/religions/{entity}/create
7. /admin/religions/commands/link
8. /admin/religions/reminder-wizard
9. /api/admin/religions/{entity}

## B.1) Auth and Reach Admin (UI Flow)

If you do not already have a user, create one first via API signup:

```bash
curl -sS -X POST "http://localhost:8080/api/auth/signup" \
	-H "Content-Type: application/json" \
	-d '{
		"name":"Admin Tester",
		"email":"admin@example.com",
		"password":"Password123!",
		"password_confirmation":"Password123!"
	}'
```

Then complete auth in browser:

1. Open http://localhost:8080/login
2. Login with email and password used above.
3. Confirm redirect to http://localhost:8080/dashboard
4. Open admin page: http://localhost:8080/admin/religions
5. Open bot setup page: http://localhost:8080/bots/setup

Expected:

1. Login succeeds and session is created.
2. Dashboard opens without auth error.
3. Admin pages load and CRUD forms are visible.

## C) Bot Setup UI

1. Open /bots/setup.
2. Submit Add Bot Token form with telegram token.
3. Confirm success message and bot appears in Registered Bots.

Expected:

1. One row in messenger_bots.
2. Bot can be selected in connect/discover forms.

## D) Group Linking and Discovery

Manual link:

1. In /bots/setup, choose bot.
2. Enter Telegram chat id and submit Link Group.

Discovery:

1. Add bot to Telegram group.
2. Send any message in that group.
3. Click Discover Groups.

Expected:

1. Group appears in Linked Groups.
2. Row exists in messenger_group_links.

### Local fallback (no webhook URL)

If Telegram webhook is not configured yet, run temporary polling mode locally:

```bash
make telegram-poll
```

Or target one bot explicitly:

```bash
make telegram-poll BOT_ID=2
```

Preferred runtime for reminder testing (poller + scheduler):

```bash
make telegram-runtime BOT_ID=2
```

Restart runtime cleanly (clears stale pollers and cache):

```bash
make telegram-runtime-restart BOT_ID=2
```

Stop runtime workers:

```bash
make telegram-stop
```

Notes:

1. This pulls updates roughly every second.
2. Keep runtime process running while testing Telegram commands and scheduled reminders.
3. Stop it with Ctrl+C.

## E) Religions Admin CRUD Page

1. Open /admin/religions.
2. Use left sidebar to open an entity list page (example: /admin/religions/religions).
3. Open create page from action button or directly at /admin/religions/religions/create.
4. Submit create form.
5. Use row Edit action to open dedicated edit page /admin/religions/religions/{id}/edit.
6. Update and save from edit page.
7. Delete row from list page action.
8. Confirm each operation is on its own page.

Expected:

1. Success toast/message for each operation.
2. Rows table reflects create/update/delete.
3. Table headers match selected entity fields.
4. List, Create, Edit, and Link Command are separate pages/routes.

Confession-specific check:

1. Open /admin/religions/confessions or confession edit page.
2. Set Welcome Message for a confession (for example: "Welcome to our confession community").
3. Save.

Expected:

1. Welcome Message persists in `confessions.welcome_message`.
2. When a user joins that confession in Telegram, this message is used first.
3. If empty, bot falls back to generic `Welcome`.

Description format check:

1. Open create/edit pages for:

- /admin/religions/religions
- /admin/religions/confessions
- /admin/religions/rituals

2. For each entity, set `Description Format` to each value in separate tests:

- plain
- markdown
- wysiwyg

3. Save and reopen edit page.

Expected:

1. Selected `Description Format` persists correctly.
2. No validation errors for allowed values.
3. Invalid value is rejected by server validation.

## E.1) Table Filters and Pagination

1. Open /admin/religions/religion_commands.
2. Set Search to chapter and click Apply.
3. Set Status to active and click Apply.
4. Change Rows to 10 and click Apply.
5. Use Next and Prev controls to navigate pages.
6. Click Reset to clear filters.

Expected:

1. URL query includes q, status, per_page, and page when used.
2. Showing X-Y of Z updates according to filters.
3. Page indicator and Next/Prev behave consistently.

## E.2) Table Sorting

1. Open /admin/religions/religion_commands.
2. Click the ID header; confirm sort direction toggles on repeated clicks.
3. Click the Name header; confirm rows are sorted alphabetically.
4. Click the Updated At header; confirm newest/oldest toggle.
5. Combine sorting with filters (q/status/per_page), then click Next page.

Expected:

1. URL query includes sort and dir after header click.
2. Header shows directional marker for current sorted column.
3. Sort remains applied while paginating and filtering.

## F) Command Mapping and Reminder Wizard

1. Open dedicated page /admin/religions/commands/link.
2. Create mapping examples:

- confession: seeded confession
- command_key: weekly-chapter
- tg_command: /chapter

- confession: seeded confession
- command_key: ritual-explain
- tg_command: /ritual

3. Save each mapping.

Expected:

1. Rows in religion_commands.
2. Rows visible in the rows table and editable through entity edit action.

Reminder Setup Wizard (same page):

1. Open /admin/religions/commands/link.
2. In `Reminder Setup Wizard`, submit one full setup:

- confession: your confession
- command_key: morning-note
- tg_command: /morningNote
- reminder_title: Morning Note
- reminder_type: pick an active type
- implementation: optional
- ritual: select your ritual (recommended)
- text_format: plain|markdown|wysiwyg
- schedule_preset: command or daily|weekly|monthly|yearly
- schedule_time: HH:MM (required for non-command presets)
- weekly_day/monthly_day/yearly_month/yearly_day: fill according to preset

3. Save.

Expected:

1. Upserted row in `religion_commands`.
2. New row in `religion_reminders` with:

- `command_id` linked to created/updated command
- `content_text` copied from selected ritual description when ritual is selected
- `frequency_mode = command` for command preset, otherwise `cron`
- `frequency_value` generated from selected preset/day/time inputs
- `command_trigger = /morningNote`

Timezone and word-mode checks:

1. In reminder setup or reminder CRUD, set `Schedule Timezone` (default should be `Europe/Kyiv`).
2. Enable `word_mode` on one reminder and attach a language pack that has active words.
3. Confirm reminder list page shows timezone column/value.

Expected:

1. Time input is native HH:MM field (not free text).
2. Reminder stores selected timezone.
3. `word_mode` reminder content is generated from `language_words` first, then falls back to legacy words JSON if needed.

## G) Command Dispatch Runtime

Built-in commands support camel token format and are available before mapped commands:

1. `/help`
2. `/aboutme`
3. `/follower` or `/follower <confession-slug>`
4. `/religionList`
5. `/religionChoose_<religion-token>__<confession-token>`
6. `/religionDescribe_<religion-token>`
7. `/confessionDescribe_<confession-token>`
8. `/confessionLeave_<confession-token>`
9. `/ritualList`

Mapped runtime commands still require:

1. At least one active `user_group_religion_preferences` row for sender + linked group.
2. Active `religion_commands` row for one of the user-selected confessions.

Seeded data already creates starter rows. For real Telegram users/groups, use `/religionChoose_<religion-token>__<confession-token>` in group chat.

### Minimal DB check SQL

```sql
select id, driver, external_user_id, username from messenger_users order by id desc;
select id, messenger_bot_id, external_chat_id, title from messenger_group_links order by id desc;
select id, messenger_user_id, messenger_group_link_id, confession_id, is_active from user_group_religion_preferences order by id desc;
select id, confession_id, command_key, trigger, is_active from religion_commands order by id desc;
select id, confession_id, command_id, title, frequency_mode, command_trigger, is_active from religion_reminders order by id desc;
```

## H) Telegram Command Tests

In your Telegram group:

1. Send /help
Expected:
- Bot replies with built-in camel token command list.
- Reply includes dynamically generated quick examples for existing religions/confessions.
- Reply includes active mapped commands from DB.
- Reply mentions multi-religion/confession support and shows selected religions + selected confessions for current group context.

2. Send /religionList
Expected:
- Bot replies with active religions and generated quick command aliases.

3. Send /religionDescribe_religion_1
Expected:
- Bot replies with religion description and confession count.

4. Send /religionChoose_religion_1__confession_1
Expected:
- Bot sends welcome block:
	- confession `welcome_message` if set
	- otherwise generic `Welcome`
	- then religion description + confession description

5. Send /religionChoose_ghuzel__original
Expected:
- User can join an additional confession without removing the previous one.

6. Send /help
Expected:
- Shows multiple selected confessions for current user/group.

7. Send /confessionDescribe_confession_1
Expected:
- Bot replies with confession description and parent religion.

8. Send /confessionLeave_confession_1
Expected:
- Bot confirms the confession was left.
- Other joined confessions stay active.

9. Send /ritualList
Expected:
- Bot lists active rituals across all currently selected confessions.

10. Send /follower
Expected:
- Bot lists active followers in the current group grouped by confession.

11. Send /follower original
Expected:
- Bot lists only active followers for confession slug `original` in the current group.

12. Send /morningNote
Expected:
- Bot replies with content from the reminder linked to that command.
- If recipients are present for the selected confession in the group, prefix is `Dear @user1, @user2`.

13. Word-of-day once-per-day guard
Steps:
- Use a reminder with `word_mode = true` and active followers in group.
- Force dispatch twice the same day:

```bash
docker compose exec -T app php artisan religions:dispatch-due-reminders --reminder-id=<ID> --force
docker compose exec -T app php artisan religions:dispatch-due-reminders --reminder-id=<ID> --force
```

Expected:
- First run sends message(s).
- Second run for same group/day sends 0 new messages.
- Reminder `meta_json` includes:
	- `last_dispatched_at`
	- `word_mode_last_sent_by_group.{groupId}=YYYY-MM-DD`

14. Command reply mention style
Steps:
- Trigger a mapped reminder command in Telegram group (for example `/morningNote`).

Expected:
- Mention line format is `Dear @username1, @username2`.
- No `tg://user?id=...` links are included.

15. Command-link admin: user selection listing
Steps:
- Open `/admin/religions/commands/link`.

Expected:
- Card `User Religion & Confession Selections` is visible.
- Table shows user, group, religions, confessions for active preferences.

16. Bundle import/export (commands page)
Steps:
- On `/admin/religions/commands/link`, use `Bundle Import / Export (TXT)` card.
- Export for confession id (default sample is confession #2).
- Import edited text back.

Expected:
- TXT includes `[RELIGION] [CONFESSION] [LANGUAGES] [RITUALS] [REMINDERS] [WORDS]` sections.
- Words include `phrases` field.
- Import success flash includes words count.

13. Send a mapped command alias shown in `/help` under Mapped commands.
Expected:
- Bot executes mapped handler or reminder content fallback.

14. Send /confessionLeave_unknown
Expected:
- Bot replies that confession was not found or user is not joined to it.

## I) Fallback Behavior

1. If command trigger does not match any religion_commands row for user confession, bot should not respond.
2. If handler class is not resolvable, webhook should stay resilient (no crash), and update should still be stored.

## J) Stored Update Verification

Verify updates persisted:

```sql
select id, messenger_bot_id, driver, external_update_id, created_at from messenger_updates order by id desc limit 20;
```

Expected:

1. Each webhook event creates a messenger_updates row.

## K) Suggested Regression Sweep

1. Re-open /bots/setup and /admin/religions after command tests.
2. Ensure command mapping still loads and no validation regressions.
3. Re-run route:list and confirm no route removals.
