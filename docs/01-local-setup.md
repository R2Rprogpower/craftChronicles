# Local Setup

## Requirements

- Docker + Docker Compose
- GNU Make (optional, for helper commands)

## Setup

```bash
git clone https://github.com/R2Rprogpower/guzleaks.git .
cp .env.example .env
docker compose up -d --build

docker compose exec -T app composer install
docker compose exec -T app php artisan key:generate

# One-time package publishes used in this project
docker compose exec -T app php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
docker compose exec -T app php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

docker compose exec -T app php artisan migrate
docker compose exec -T app php artisan optimize:clear

make install-hooks
```

## Service URLs

- API / app (Nginx): http://localhost:8080
- PostgreSQL: localhost:5432
- Redis: localhost:6379
- pgAdmin: http://localhost:5050
  - Email: admin@example.com
  - Password: admin

## Database defaults (.env)

- DB host: db
- DB port: 5432
- DB name: app
- DB user: app
- DB password: app

## Useful commands

```bash
# Start/stop
make up
make build

# Code quality
make fmt
make lint
make test
make check

# Laravel commands
docker compose exec -T app php artisan migrate
docker compose exec -T app php artisan migrate:fresh
docker compose exec -T app php artisan route:list --path=api
```

## Telegram bot onboarding (local)

1. Start app and run migrations.
2. Open `http://localhost:8080/bots/setup`.
3. In "Add Bot Token":
4. Select driver `telegram`.
5. Paste BotFather token and save.
6. Add the bot to your Telegram group.
7. Send one message in the group.
8. Back in setup page, click "Discover Groups" for that bot.
9. Optionally use "Connect Group" manually with known chat ID.
10. Optional webhook mode:
11. Expose local app with a public tunnel (for example ngrok or cloudflared).
12. Register webhook URL like `https://<public-host>/api/telegram/webhook/{botId}`.

### Polling vs webhook in local dev

- Polling discovery is easiest locally and needs no public URL.
- Webhook mode is closer to production and needs public HTTPS.

## Notes

- This project uses PostgreSQL, so use pgAdmin.
- If you run Artisan locally (outside Docker), ensure required PHP extensions are installed.
