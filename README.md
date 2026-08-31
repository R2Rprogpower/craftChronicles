# API (Laravel 12)

This repository documentation is split into focused files in `docs/` for easier navigation and maintenance.

## Documentation index

1. [Local setup](docs/01-local-setup.md)
2. [API overview](docs/02-api-overview.md)
3. [Module database structure](docs/03-module-database.md)
4. [OpenAPI and docs](docs/04-openapi.md)
5. [Production deploy (blue-green)](docs/05-production-deploy.md)
6. [Rollback and backups](docs/06-rollback-and-backups.md)
7. [Security and troubleshooting](docs/07-security-and-troubleshooting.md)
8. [Template repo workflow (multi-app)](docs/08-template-repo-workflow.md)
9. [Judaism pack TODO](docs/09-judaism-pack-todo.md)
10. [Production TODO](PRODUCTION_TODO.md)
11. [Personal brand platform](docs/09-personal-brand-platform.md)
12. [Personal brand evaluation and tuning guide](docs/10-brand-evaluation-and-tuning.md)

## Operational quick links

1. [Local startup runbook](STARTUP.md)
2. [Manual testing guide](MANUAL_TESTING.md)
3. [Implemented features snapshot](IMPLEMENTED.md)

Built-in Telegram commands now include `/aboutme` for appended Markdown doc output and `/follower` for group follower-by-confession listing.

`/aboutme` attachments also include `ABOUTME_IMPORT_PATTERN.md` with a full ready-to-use bundle import template.

## Bot onboarding UI

After local setup and migrations, open:

- `http://localhost:8080/bots/setup`

This page supports:

1. Saving/verifying a bot token (Telegram today, extensible by driver).
2. Linking a group manually by chat ID.
3. Discovering groups from recent updates after the bot is added to a group.
4. Registering a webhook URL when using a public tunnel.
## Quick start

```bash
git clone https://github.com/R2Rprogpower/guzleaks.git .
cp .env.example .env
docker compose up -d --build
docker compose exec -T app composer install
docker compose exec -T app php artisan key:generate
docker compose exec -T app php artisan migrate
```

For full setup and production operations, use the docs index above.
