# Module-local Database Structure

Each module can define its own database artifacts directly inside the module:

- `app/Modules/<Module>/Database/Migrations/*.php`
- `app/Modules/<Module>/Database/Seeders/*Seeder.php`
- `app/Modules/<Module>/Database/Factories/*Factory.php`

## How it works

- Module migrations are auto-loaded by `App\Providers\ModuleDatabaseServiceProvider`.
- Module seeders are auto-discovered by `database/seeders/DatabaseSeeder.php`.
- Factories can live in the same module and be referenced from module models via `newFactory()`.

## Example commands

```bash
docker compose exec -T app php artisan migrate
docker compose exec -T app php artisan db:seed
docker compose exec -T app php artisan migrate:fresh --seed
```

## Messenger module tables

The onboarding and connector layer adds module-local migrations under `app/Modules/Messenger/Database/Migrations`.

Core tables:

1. `messenger_bots`
2. `messenger_group_links`
3. `messenger_updates`

Design notes:

1. `messenger_bots.bot_token` is encrypted via model cast.
2. `messenger_group_links` stores external group identifiers per bot.
3. `messenger_updates` stores raw incoming updates for discovery and debugging.
