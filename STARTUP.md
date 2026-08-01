# Local Startup Guide

This project is a Laravel-based multi-religion Telegram bot platform derived from craftChronicles.

## Prerequisites

- Docker and Docker Compose
- Git
- A Telegram bot token from BotFather

## 1) Configure Environment

1. Copy environment template:

```bash
cp .env.example .env
```

2. Update required values in .env:
- APP_NAME
- APP_URL (for example: http://localhost)
- DB_* values if you are not using defaults from Docker Compose
- REDIS_* values if needed

3. Generate application key:

```bash
docker compose run --rm app php artisan key:generate
```

## 2) Build and Start Services

```bash
docker compose up -d --build
```

## 3) Install Dependencies (if not baked into image)

```bash
docker compose run --rm app composer install
docker compose run --rm app npm install
```

## 4) Database Setup

```bash
docker compose run --rm app php artisan migrate
docker compose run --rm app php artisan db:seed --class="App\\Modules\\Religions\\Database\\Seeders\\ReligionsSeeder"
```

## 5) Start Asset Build (optional for admin pages)

```bash
docker compose run --rm app npm run build
```

For watch mode during local development:

```bash
docker compose run --rm app npm run dev
```

## 6) Verify Key Routes

- Health check: GET /api/health
- Bot setup page: GET /bots/setup
- Religions admin page: GET /admin/religions
- Telegram webhook endpoint: POST /api/telegram/webhook/{botId}

## 7) Telegram Onboarding Flow

1. Open /bots/setup
2. Save bot with:
- name
- driver: telegram
- token from BotFather
3. Link a Telegram group by chat id or use discover groups
4. Register webhook to point at:
- /api/telegram/webhook/{botId}

## 8) Create Command Mapping

1. Open /admin/religions/religion_commands
2. In Quick Link Command:
- pick confession
- set internal command key (example: weekly-chapter)
- set Telegram trigger (example: /chapter)
3. Save mapping

## 9) Sidebar CRUD Admin

1. Open /admin/religions
2. Use the left sidebar to switch entities.
3. For each entity page:
- create rows using structured form inputs
- edit rows from the table row action
- delete rows from the table row action

Examples:
- /admin/religions/religions
- /admin/religions/confessions
- /admin/religions/rituals
- /admin/religions/religion_reminders

## 10) Command Dispatch Runtime

When a Telegram group message starts with a slash command:

1. Webhook ingests the update
2. The platform resolves group and messenger user
3. It loads the user group preference and confession
4. It resolves trigger in religion_commands
5. It dispatches the command handler class and sends the handler response back to Telegram

Built-in command behavior now supports multi-selection in a group:

- `/help` includes selected religions and selected confessions for the sender/group.
- `/follower` lists active group followers by confession, with optional confession slug filter.
- `/religionChoose_<religion>__<confession>` can be used multiple times to join multiple confessions.
- `/confessionLeave_<confession>` removes only that confession selection.

Mapped reminder replies and scheduled reminders mention followers as:

- `Dear @username1, @username2`

For `word_mode` reminders, delivery is capped to once per day per group (tracked in reminder meta).

## 11) Runtime Commands (Polling + Scheduler)

For local runtime where webhook is unavailable or for scheduled reminder testing:

```bash
# Start poller + scheduler (requires BOT_ID)
make telegram-runtime BOT_ID=2

# Restart cleanly if polling conflicts/stale cache
make telegram-runtime-restart BOT_ID=2

# Stop running poller/scheduler processes
make telegram-stop
```

If you only need polling without scheduler:

```bash
make telegram-poll BOT_ID=2
```

## 12) Admin Operations Snapshot

Main operational page for command setup and bundle operations:

- `/admin/religions/commands/link`

This page now includes:

1. Command mapping form.
2. Reminder setup wizard (with timezone field, Kyiv default).
3. Recent command links table.
4. User religion/confession selection listing.
5. Bundle import/export card (TXT sections including words).

## 13) Useful Commands

```bash
# Show routes
docker compose run --rm app php artisan route:list

# Tail Laravel logs
docker compose exec app sh -lc "tail -n 200 storage/logs/laravel.log"

# Run tests
docker compose run --rm app php artisan test
```

## 14) Troubleshooting

- If migrations fail, ensure postgres container is healthy and DB credentials match .env.
- If webhook does not receive updates, verify public URL and webhook registration result on /bots/setup.
- If Telegram poll returns 409 conflicts, stop duplicate pollers and restart runtime using `make telegram-runtime-restart BOT_ID=<id>`.
- If command mapping saves but nothing responds, ensure:
  - group is linked to bot
  - user has at least one active row in user_group_religion_preferences for that group
  - religion_commands trigger matches exact command token (example: /chapter)
  - mapped handler class exists
