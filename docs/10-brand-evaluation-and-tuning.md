# Personal Brand Evaluation and Tuning

## Production links

Public:

- Portfolio: <https://ruslanrahimov.space/portfolio>
- Progress dashboard: <https://ruslanrahimov.space/progress>
- Existing application root: <https://ruslanrahimov.space/>
- API health: <https://ruslanrahimov.space/api/health>

Operational (do not share credentials):

- PgAdmin: <https://pgadmin.ruslanrahimov.space>
- GitHub repository: <https://github.com/R2Rprogpower/craftChronicles>
- GitHub Actions: <https://github.com/R2Rprogpower/craftChronicles/actions>

Local equivalents:

- Portfolio: <http://localhost:8080/portfolio>
- Progress: <http://localhost:8080/progress>
- PgAdmin: <http://localhost:5050>

## Ten-minute acceptance pass

1. Open `/portfolio` at desktop width and on a phone.
2. Confirm the first screen answers: who Ruslan helps, what he does, and why the
   visitor should continue.
3. Check every work item. Mark any claim that lacks proof, a link, a screenshot, or
   a concrete result. Do not manufacture metrics to make a card look serious.
4. Check services for overlap. Each service should name a buyer, a problem, an
   outcome, and a sensible first engagement.
5. Open every `Work with me` CTA. Submit one test request with email and one with a
   Telegram contact only. Confirm both success and validation states.
6. Open `/progress`. Verify every percentage and milestone reflects reality rather
   than optimism in a nice jacket.
7. Test all GitHub/Telegram links and keyboard navigation through the modal.
8. Record changes as one of: copy, evidence/media, offer, progress, or engineering.

## Where to tune content

The current baseline is content-as-code in idempotent module seeders:

- positioning, bio, expertise, social links, contact, FAQ:
  `app/Modules/PersonalBrand/Database/Seeders/PersonalBrandSeeder.php`;
- portfolio items:
  `app/Modules/Portfolio/Database/Seeders/PortfolioSeeder.php`;
- services:
  `app/Modules/Services/Database/Seeders/ServicesSeeder.php`;
- products/experiments:
  `app/Modules/Products/Database/Seeders/ProductsSeeder.php`;
- planned content:
  `app/Modules/Content/Database/Seeders/ContentSeeder.php`;
- progress areas and milestones:
  `app/Modules/BrandProgress/Database/Seeders/BrandProgressSeeder.php`.

After editing locally:

```bash
docker compose exec -T app php artisan db:seed \
  --class='App\Modules\PersonalBrand\Database\Seeders\BrandPlatformSeeder' --force
npm run build
```

Reload `/portfolio` and `/progress`. The seeders use stable slugs/keys and
`updateOrCreate`, so they update baseline records rather than duplicate them.

Important: during the current content-as-code phase, a production deploy reapplies
the baseline seeder. Do not make lasting copy changes directly in PgAdmin; place them
in the seeders and deploy. This rule should be removed when an authenticated content
editor becomes the source of truth.

## Service request review

Until the internal lead screen exists, inspect requests without exposing credentials:

```bash
docker compose exec -T app php artisan tinker
```

Then:

```php
App\Models\ServiceRequest::query()
    ->with('service:id,name')
    ->latest()
    ->limit(20)
    ->get(['id', 'name', 'email', 'contact', 'service_offering_id', 'status', 'created_at']);
```

The message and budget are intentionally omitted from this quick list; query a single
record by ID when needed. Do not paste production leads into public issue trackers.

## Deployment and verification

Pushes to `main` run Docker CI and then the blue-green production deployment. The
deploy migrates, applies only `BrandPlatformSeeder` (not demo users), runs tests on an
isolated database, switches Caddy, and stops the old color.

Monitor:

1. Open GitHub Actions and wait for the current `CI (Docker identical)` run.
2. Confirm `docker-checks` and `deploy` are green.
3. Check `/api/health`, `/portfolio`, and `/progress` return HTTP 200.
4. Submit a service request and verify it appears in `service_requests`.
5. If deployment fails, do not rerun blindly; read the failed step and use
   `docs/06-rollback-and-backups.md` when traffic was already switched.

## Recommended feedback format

Use one item per line:

```text
[page/section] current problem -> desired outcome
```

Examples:

```text
[portfolio/hero] too generic -> emphasize Laravel modernization and mentoring
[portfolio/work] Automation Control lacks proof -> add architecture screenshot
[progress/youtube] 15% is too high -> set 5% and make pilot outline the next step
```
