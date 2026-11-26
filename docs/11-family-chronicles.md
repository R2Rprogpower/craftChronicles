# Family Chronicles MVP

Family Chronicles is an authenticated module for collecting a person's biography one question at a time. It is intentionally implemented inside the existing Laravel application without new services or frontend dependencies.

## Production URL

[https://api.craftchronicles.site/family-chronicles](https://api.craftchronicles.site/family-chronicles)

Unauthenticated users are redirected to the existing `/login` page.

## Implemented scope

- Multiple independent application users through the existing authentication system.
- Multiple people within each user's private family workspace.
- Optional birth year and relationship for each person.
- 183 stable, curated questions across 15 thematic categories.
- Timeless questions and 11 chronological life stages.
- Automatic exclusion of stages the person has not yet lived through when a birth year is known.
- Stable question of the day per user, person, and calendar date.
- Story mode with chronological ordering.
- Random mode with category and life-stage filters.
- Text answers up to 100,000 characters.
- Browser autosave after a short pause.
- Local browser draft backup for temporary network failures.
- Answered, draft, deferred, and skipped states.
- Private uploads for JPEG, PNG, WebP, MP3, WAV, OGG, WebM, MP4, QuickTime video, and PDF.
- 50 MB application upload limit per file.
- Private file download through an authenticated ownership check; uploads are not exposed through a public storage URL.
- Progress bar per person.
- Responsive large-type interface intended for older users.

## Data model

| Table | Purpose |
| --- | --- |
| `family_people` | People whose biographies are being collected; owned by one application user. |
| `biography_answers` | One current answer/state for a person and stable question key. |
| `biography_attachments` | Private files linked to an answer. |
| `biography_daily_questions` | Stable question-of-the-day selection. |

The question bank lives in `QuestionBank.php` rather than database rows. Stable keys allow the wording and collection to evolve without copying 183 rows for every user.

## Privacy model

- Every route requires the existing `auth` middleware.
- People, answers, daily questions, and attachments carry the authenticated owner ID.
- Controllers resolve records through the authenticated owner or return `404`.
- Files are stored on Laravel's private `local` disk.
- Download and deletion require an ownership check.
- The client never receives filesystem paths.

This MVP does not yet implement sharing between accounts. That is deliberate: accidental cross-family disclosure is more harmful than missing collaboration.

## Main files

- `app/Modules/FamilyChronicles/web.php`
- `app/Modules/FamilyChronicles/Http/Controllers/FamilyChroniclesController.php`
- `app/Modules/FamilyChronicles/Services/QuestionBank.php`
- `app/Modules/FamilyChronicles/Models/*`
- `app/Modules/FamilyChronicles/Database/Migrations/*`
- `resources/views/family-chronicles/index.blade.php`
- `public/css/family-chronicles.css`
- `public/js/family-chronicles.js`
- `tests/Feature/FamilyChroniclesFeatureTest.php`

## Local use

```bash
docker compose up -d
docker compose exec -T app php artisan migrate
```

Open `http://localhost:8080/family-chronicles` after signing in.

## Automated checks

```bash
php artisan test tests/Feature/FamilyChroniclesFeatureTest.php
```

The focused suite verifies authentication, user isolation, stable daily questions, answer persistence, private upload/deletion, question-bank size, and future-stage exclusion.

## Known MVP limitations

- Russian UI and question text only; the person locale is stored for future localization.
- Existing accounts must be created through the current Craft Chronicles user/admin flow.
- Files can be uploaded, but direct in-browser microphone/camera recording is deferred.
- No family tree, relationship graph, GEDCOM, narrative book generation, transcription, notifications, or AI processing yet.
- No account-to-account family sharing or granular per-answer permissions yet.
- No answer version history or export yet.
- Upload retry is manual after an error; text drafts have automatic local recovery.

See `FAMILY_CHRONICLES_COVERAGE.md` for the exact coverage against the long-form specification.
