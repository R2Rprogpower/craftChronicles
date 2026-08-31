# Project State

Last updated: 2026-08-31

## Current product

CraftChronicles is a Laravel 12 modular monolith. It originally provided API-first
Users, Auth, and Permissions modules plus a Blade admin theme. Iteration 1 adds a
public React personal-brand foundation without replacing those working surfaces.

## Implemented

- Public `/portfolio` website with Hero, expertise, work, services, products,
  content, contact, FAQ, and reusable request modal.
- Public `/progress` dashboard with areas, status, percentages, milestones, and
  concrete next steps.
- Real service request persistence at `POST /service-requests`, with validation,
  CSRF protection, throttling, and structured response.
- Configurable database-backed brand, portfolio, service, product, content, and
  progress data with idempotent module seeders.
- React 19 integration through the existing Vite pipeline.
- Feature coverage for public pages, successful enquiries, and validation.
- Docker `pdo_sqlite` support aligned with the existing PHPUnit configuration.

## Verification state

- Full PHPUnit suite: 43 tests, 101 assertions, passing.
- PHPStan level 6: passing with 512 MB CLI memory limit.
- Laravel Pint: passing after formatting.
- Vite production build: passing; legacy theme Sass emits existing deprecation and
  unresolved-font warnings documented in `TODO.md`.
- Local Docker PostgreSQL migrations/seeders applied additively; HTTP smoke tests for
  `/portfolio` and `/progress` both returned 200.
- Production deploy now applies the targeted `BrandPlatformSeeder` only. Evaluation,
  tuning, request-review, and deployment links are in `docs/10-brand-evaluation-and-tuning.md`.

## Important constraints

- `docker-compose.yml` had pre-existing uncommitted localhost-binding changes.
  They are preserved and unrelated to this iteration.
- `/` remains the existing welcome page. The platform is intentionally served at
  `/portfolio` to avoid silently changing existing template behavior.
- Seed content is an initial working draft, not validated marketing truth. Do not
  invent metrics or commercial outcomes.
- The rebased `origin/main` contains concurrent Messenger/Religions/Telegram work
  with 197 pre-existing PHPStan findings. A committed baseline isolates that debt;
  personal-brand code remains clean and new static-analysis findings still fail CI.
