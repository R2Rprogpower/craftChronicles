# Personal Brand Platform

## Public surfaces

- `GET /portfolio` — React portfolio and personal brand website.
- `GET /progress` — public progress dashboard.
- `POST /service-requests` — validated service enquiry submission (10 requests/minute/IP).

The original `/` route is deliberately unchanged. The new platform is mounted on
explicit routes so existing consumers and the template smoke test remain stable.

## Data ownership

Public copy is structured domain data, not JSX content. Seeders provide an initial
editable baseline and can be replaced by an admin UI later:

- `brand_profiles`: positioning, bio, expertise, social/contact data, FAQ;
- `portfolio_items`: extensible work/case-study records and structured links/media;
- `service_offerings`: active services, benefits, engagement format, CTA;
- `content_items`: YouTube, Twitch, article, tutorial, and experiment pipeline;
- `products`: product stage, links, and roadmap;
- `brand_progress_areas` + `progress_milestones`: goals and visible execution state;
- `service_requests`: persisted enquiries, ready for notifications/CRM integration.

JSON columns are used for bounded lists whose schema is still evolving (links,
benefits, roadmap, expertise). First-class entities and operational records use
relational tables. This avoids a premature CMS while keeping copy configurable.

## Backend request flow

Public page reads follow the existing project vocabulary:

`BrandPlatformController -> PageProcessor -> BrandPlatformService -> BrandPlatformRepository -> Presentation`

The repository is an intentional cross-module read model for composing public pages.
Writes remain module-owned. Service enquiries use the full existing write pattern:

`ServiceRequestController -> StoreRequest -> StoreProcessor -> DTO -> Service -> Repository -> Model -> Presentation`

## Frontend structure

React is mounted by `resources/views/brand-platform.blade.php`, using Laravel/Vite.
The server injects the already-presented page payload. React does not query multiple
endpoints during first render, avoiding a second application boundary.

Frontend files live in `resources/js/brand-platform/`:

- `pages/`: route-level composition only;
- `sections/`: portfolio and dashboard sections;
- `components/`: header, headings, tags, and enquiry modal;
- `resources/css/brand-platform.css`: isolated responsive visual system.

The enquiry form posts JSON with the Blade-provided CSRF token. Laravel validation
requires name, message, and at least one of email/contact. Company, service, and
budget are optional.

## Content updates

Until an admin interface exists, update the relevant module seeder, then run:

```bash
docker compose exec -T app php artisan migrate --force
docker compose exec -T app php artisan db:seed --force
```

Seeders use `updateOrCreate` and are safe to run repeatedly. They do not fabricate
service requests.

Production deploys invoke the targeted `BrandPlatformSeeder`; the general
`DatabaseSeeder` is not used, so demo users are not created. See
`docs/10-brand-evaluation-and-tuning.md` for review and tuning instructions.

## Verification

```bash
docker compose exec -T -e APP_ENV=testing app php artisan test
docker compose exec -T app vendor/bin/phpstan analyse --no-progress --memory-limit=512M
docker compose exec -T app vendor/bin/pint --test
npm run build
```

The container includes `pdo_sqlite` because `phpunit.xml` explicitly uses an
in-memory SQLite database.
