# Production TODO

This is a shared production readiness backlog for the platform, not specific to one religion pack.

## 1. VPS and Infrastructure

- Buy or provision a VPS for production runtime.
- Decide minimum production footprint:
  - app
  - postgres
  - redis
  - worker / scheduler runtime
  - reverse proxy

## 2. Deployment Readiness

- Prepare deployment checklist:
  - production env file
  - bot token and webhook config
  - domain / subdomain
  - HTTPS certificates
  - database backups
  - restart / rollback steps
- Add deployment smoke checks:
  - webhook receives updates
  - poller/scheduler are not duplicated
  - word-of-day posts once per group
  - admin pages open only for allowed users

## 3. Admin Exposure and Security

- Expose admin UI safely to real users:
  - decide whether admin lives on main app domain or a separate subdomain
  - require authenticated access
  - restrict by role / permission
  - review whether extra IP allowlist or MFA gate is needed for production

## 4. Permissions and Access Model

- Ensure permissions exist for all sensitive actions:
  - view admin
  - manage religions
  - manage confessions
  - manage rituals
  - manage language packs
  - manage words
  - manage reminders
  - run smoke tests
  - import/export bundles
  - relink bot/group
  - manage deployment-related settings if exposed in UI
- Map permissions to roles:
  - super-admin
  - religion editor
  - language editor
  - reminder operator
  - read-only reviewer
- Verify no admin page or action is exposed without explicit permission checks.
- Add permission tests for:
  - page access
  - CRUD actions
  - import/export actions
  - command-link page tools
  - smoke-test buttons

## 5. Production Milestone

- M4 Production readiness:
  - VPS provisioned
  - deployment runbook tested
  - admin exposure locked down with permissions

## 6. Pre-commit Recovery

- Temporary state: pre-commit hooks are disabled locally for active development.
- Add a cleanup pass to fix all issues highlighted by pre-commit checks:
  - run formatter/lint/type checks from `scripts/pre-commit`
  - fix PHPStan findings and failing unit tests
  - remove temporary suppressions/workarounds introduced during rapid changes
- Re-enable hooks after cleanup and verify by running one full local commit flow.
