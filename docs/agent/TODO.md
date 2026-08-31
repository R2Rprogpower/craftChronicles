# Next Tasks

## Product/content

1. Review positioning, biography, offers, and public language with Ruslan.
2. Replace draft portfolio summaries with evidence-backed case studies, screenshots,
   links, constraints, contribution, and outcomes.
3. Decide whether `/portfolio` should become the production root route.
4. Validate the first YouTube, Twitch, and product hypotheses with real users.
5. Use `docs/10-brand-evaluation-and-tuning.md` for the first structured review.

## Platform

1. Add authenticated CRUD screens for brand content; reuse current domain tables.
2. Queue Telegram/email notification after a service request is persisted.
3. Add request status workflow (`new`, `contacted`, `qualified`, `closed`) and notes.
4. Add SEO/OpenGraph metadata and local/self-hosted font assets.
5. Add browser-level accessibility and responsive visual regression tests.
6. Add frontend linting (ESLint) once frontend contribution grows beyond this slice.

## Technical debt discovered, not rewritten

- Existing Vite build copies the complete `resources/js`, `resources/css`, and large
  legacy library tree into `public/build` after generating hashed assets.
- Legacy Bootstrap theme Sass emits hundreds of Dart Sass deprecation warnings.
- Legacy theme CSS has unresolved font/image URLs during Vite compilation.
- `npm audit` reports 7 issues (1 low, 4 high, 2 critical), largely in the inherited
  asset/tooling dependency graph. Audit and upgrade deliberately; do not run a blind
  major-version `npm audit fix`.
- Docker builds compile extensions and retain many build-time `-dev` packages,
  producing a large image. A multi-stage extension build is a later optimization.
- PHPStan needs `--memory-limit=512M`; encode that in a Composer script later.
- Concurrent Messenger/Religions/Telegram work arrived on `origin/main` with 197
  PHPStan level-6 findings. They are captured in `phpstan-baseline.neon` so CI still
  rejects new findings. Reduce the baseline module by module; do not regenerate it
  casually, because that would hide regressions.
