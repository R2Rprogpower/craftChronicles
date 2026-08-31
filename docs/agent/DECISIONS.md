# Technical Decisions

## 2026-08-31 — React as a mounted public UI

The repository claimed a Laravel/React direction but contained no React runtime.
React 19 and the official Vite React plugin were added. A Blade mount provides CSRF
and initial data. Inertia was not added because these two public pages do not need a
new navigation/data protocol; plain Laravel + React is the smaller coherent choice.

## 2026-08-31 — Database-backed structured content, no CMS

The brand platform needs configurable content but not editorial roles/workflows yet.
Domain tables plus idempotent seeders provide configurability, testability, and a
future admin path without paying the complexity cost of a CMS now.

## 2026-08-31 — Cross-module public read model

`BrandPlatformRepository` composes read-only data from several contexts. Creating a
service/repository/controller stack for each read would add ceremony without an
independent use case. Writes remain module-owned to preserve boundaries.

## 2026-08-31 — Persist service requests now

The modal is not a dead form. Requests are validated and stored immediately. Email,
Telegram, CRM, and queue delivery are deliberately deferred; persistence is the
reliable source of truth and prevents notification failures from losing leads.

## 2026-08-31 — Keep existing root route

The existing `/` smoke test and template behavior are preserved. The new website is
at `/portfolio`. Promoting it to `/` later should be an explicit product/deployment
decision after copy review and production seeding.

## 2026-08-31 — Add SQLite support to Docker

`phpunit.xml` uses SQLite in-memory, while the Docker image only shipped PostgreSQL
PDO. Adding `pdo_sqlite` makes the documented test command executable and does not
change the production database.

## 2026-08-31 — Targeted production baseline seeding

The deploy invokes `BrandPlatformSeeder` after migrations. It composes only public
brand, portfolio, services, products, content, and progress seeders; the general
database seeder is not used because it also creates demo users. During the current
content-as-code phase, seeders are authoritative and intentionally reapplied.

## 2026-08-31 — Observable CI quality gates

The former workflow wrapped formatter, PHPStan, unit tests, and feature tests inside
one `make ci-check` step. GitHub exposed only exit code 2 when it failed. The gates
are now separate non-mutating steps; this preserves identical commands while making
the failing class visible and prevents CI from formatting its own checkout.

## 2026-08-31 — Build frontend before feature tests

The public React views require Vite's generated manifest. A clean runner did not have
the locally generated `public/build` directory, so page feature tests returned 500
before deployment even though developer workspaces passed. `ci-setup` now runs
`npm ci` and the production Vite build before tests, making the runner reproducible
and making a successful frontend build a deployment gate.

## 2026-08-31 — Cache production config after the deployment test gate

`artisan optimize` previously ran before tests on the VPS. Laravel then kept the
production environment cached even when the test commands supplied `APP_ENV=testing`,
so CSRF middleware returned 419 for request tests. Optimization now runs only after
the isolated test database is removed and production dependencies are pruned.
