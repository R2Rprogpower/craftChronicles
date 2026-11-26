# Family Chronicles deployment and handover

## Deployment path

The module uses the repository's existing GitHub Actions blue-green deployment. A push to `main` runs formatting, PHPStan, unit tests, feature tests, and then the established VPS deployment.

The deploy script already runs:

```bash
php artisan migrate --force
```

No separate service, queue, cron job, environment variable, frontend build, or database seeder is required for Family Chronicles.

## Post-deployment checklist

1. Open [https://api.craftchronicles.site/family-chronicles](https://api.craftchronicles.site/family-chronicles).
2. Confirm a guest is redirected to `/login`.
3. Sign in with a test account.
4. Add a person with a birth year and relationship.
5. Confirm a question of the day appears.
6. Enter text, wait for `Черновик сохранён`, reload, and confirm restoration.
7. Mark an answer complete and confirm progress increases.
8. Switch among daily, story, and random modes.
9. Filter by topic and period.
10. Upload a small image and download it.
11. Delete the image.
12. Verify another account cannot see the first account's people or answers.

## Rollback

Application rollback uses the repository's existing blue-green rollback procedure. The migration only creates four new tables and does not modify existing data. Do not run `migrate:fresh` on production.

If application code is rolled back while the tables remain, they are inert and can safely remain until a later deployment. Dropping the tables would permanently remove biographies and attachments and therefore requires an explicit backup and owner approval.

## Backup and transfer

To transfer the application to another maintainer, provide:

- Repository access.
- Existing deployment documentation in `docs/05-production-deploy.md`.
- This document and `docs/11-family-chronicles.md`.
- Database backup containing the four Family Chronicles tables.
- Private storage backup containing `storage/app/private/family-chronicles/` (or the equivalent configured local disk root).

Never copy only the database: attachment metadata without the private files is incomplete. Never expose the private storage directory through a web server alias.

## Manual server checks

If uploads larger than the old defaults fail with HTTP 413 or validation errors, verify that the deployed Nginx configuration includes `client_max_body_size 52M` and PHP-FPM reads `public/.user.ini` (`upload_max_filesize=50M`, `post_max_size=52M`).
