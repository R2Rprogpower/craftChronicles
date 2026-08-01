# Judaism Pack TODO

This is a working backlog for implementing a full Judaism content/language pack in the Telegram bot platform.

## 1. Product Scope

- Define Judaism as a production-ready religion pack with:
  - weekly chapter flow
  - ritual-aware reminders
  - Hebrew language learning content
  - group/user preference support
- Keep pack extensible so additional sub-packs can be added later.

## 2. Core Domain Additions

- Add/confirm data entities for Judaism-specific content:
  - chapter cycles (annual and optional alternate cycle)
  - chapter units and localized titles
  - chapter text segments
  - analysis/commentary snippets
  - source attribution and license metadata
- Add/confirm language entities:
  - Hebrew dictionary entries
  - transliteration mappings
  - usage examples/phrases
  - morphology metadata (optional phase 2)

## 3. Chapter Preparation Workflow (Manual + Code-Assisted)

### 3.1 Manual Preparation SOP

- Create a weekly operator checklist:
  - choose chapter key for current week
  - verify text completeness
  - verify commentary block
  - verify attribution footer
  - verify outgoing message length constraints
- Define fallback behavior if weekly data is missing:
  - send short fallback message
  - log missing content event

### 3.2 Artisan Commands To Add

- Add a command to prepare chapter payloads:
  - `php artisan judaism:prepare-weekly-chapter --week=YYYY-WW --locale=en`
- Add dry-run rendering command:
  - `php artisan judaism:render-weekly-chapter --week=YYYY-WW --chat-id=<id> --dry-run`
- Add validation command:
  - `php artisan judaism:validate-chapter-data --week=YYYY-WW`

### 3.3 Suggested Services

- `JudaismWeeklyChapterPreparationService`
- `JudaismWeeklyChapterValidationService`
- `JudaismWeeklyChapterMessageComposer`

## 4. Hebrew Dictionary Parsing Pipeline

### 4.1 Input and Source Strategy

- Identify dictionary data sources with explicit licensing.
- Store source metadata (provider, version, license URL, fetched_at).
- Reject ingestion for unknown or incompatible license.

### 4.2 Parser/Normalizer

- Build parser to normalize:
  - lemma
  - transliteration
  - translation gloss
  - part of speech
  - optional root and morphology tags
- Normalize punctuation and whitespace consistently.
- Deduplicate by key `(language_pack_id, lemma, translation)`.

### 4.3 Commands To Add

- `php artisan judaism:import-hebrew-dictionary --file=<path> --format=json`
- `php artisan judaism:reindex-hebrew-dictionary`
- `php artisan judaism:validate-hebrew-dictionary`

### 4.4 Storage Plan

- Reuse `language_words` for base cards.
- Extend with metadata in `meta_json` for:
  - lemma variants
  - root
  - morphology
  - source reference ID
- Consider future dedicated table if metadata grows too large.

## 5. Language Learning Pack Behavior

- Add Judaism pack-specific selection command path:
  - choose religion + confession + language pack
- For word mode reminders:
  - prioritize Hebrew pack words for Judaism confessions
  - keep once-per-day-per-group guard
- Add optional formatting profile for Hebrew display:
  - RTL-safe line breaks
  - transliteration always visible
  - phrase examples included when available

## 6. Command and Runtime Integration

- Add/confirm mapped commands:
  - weekly chapter command
  - chapter explain command
  - ritual list/explain shortcuts specific to active confession context
- Ensure `/help` surfaces Judaism examples dynamically when pack is active.
- Ensure mention format remains `Dear @user1, @user2` in all Judaism reminders.

## 7. Reminder and Scheduling Rules

- Confirm timezone behavior with default `Europe/Kyiv` and per-reminder override.
- Add Judaism-specific reminder presets (examples):
  - weekly chapter day/time
  - preparation reminder for admins/moderators
- Add pre-send content checks to prevent empty chapter dispatch.

## 8. Admin UX Tasks

- Add ready-to-use import template section for Judaism in bundle docs.
- Add admin hints on command-link page for Judaism command examples.
- Add list filters for Hebrew language words:
  - search by lemma
  - search by transliteration
  - active/inactive status

## 9. QA and Manual Testing Additions

- Add manual tests for:
  - chapter preparation command success/failure
  - dictionary import with valid and invalid schema
  - word mode output containing word + translation + transcription + phrases
  - once-per-day dispatch behavior with Judaism word mode reminders
- Add regression checks for:
  - no duplicate sends
  - no broken help output
  - no timezone drift

## 10. Observability and Reliability

- Add structured logs for:
  - chapter preparation runs
  - dictionary import runs
  - skipped reminder reasons
- Add counters/metrics:
  - chapters prepared per week
  - dictionary entries imported
  - invalid entries rejected

## 11. Security and Compliance

- Restrict import/prepare commands to trusted operators.
- Record audit trail for:
  - chapter content updates
  - dictionary imports
  - schedule changes
- Avoid storing copyrighted full texts unless license explicitly allows it.

## 12. LLM Q&A Integration (Bundle-Aware)

- Add a bundle-aware LLM answer flow for free-form user questions.
- Build prompt context from active Judaism bundle data:
  - selected religion/confession
  - ritual definitions and constraints
  - chapter/commentary snippets
  - language pack terms and transliterations
- Support practical user questions such as:
  - "Can I do this during Shabbat?"
  - "Можно ли это делать в шаббат?"
- Add an answer policy:
  - clearly separate factual/source-backed guidance from uncertain guidance
  - include source references used from the bundle when available
  - show a short disclaimer that this is informational and not a formal rabbinic ruling
- Add fallback behavior:
  - when bundle context is insufficient, ask clarifying questions
  - when confidence is low, suggest consulting a qualified rabbi/community authority
- Add observability for LLM answers:
  - log which bundle entities were injected into context
  - log confidence/fallback path chosen
  - avoid logging sensitive personal data
- Add QA checks:
  - correct use of active confession context in answers
  - no cross-religion leakage in retrieved context
  - stable answers for repeated Shabbat-type scenarios

## 13. Production Readiness Tracking

- Shared production tasks are tracked in `PRODUCTION_TODO.md`.
- Keep this Judaism TODO focused on pack-specific scope.

## 14. Suggested Milestones

- M1 Data model and parser skeleton:
  - schema decisions complete
  - parser command stubs working
- M2 Chapter prep runtime:
  - prepare/validate/render commands live
  - weekly chapter dispatch integrated
- M3 Quality hardening:
  - tests added
  - docs updated
  - metrics/logging enabled

- M4 LLM Q&A bundle-aware flow:
  - context injection from Judaism bundle implemented
  - answer policy/disclaimer and fallback paths implemented
  - regression tests for Shabbat-type questions added

## 15. Open Questions

- Which canonical source(s) will be used for chapter text and commentary?
- What license constraints apply for storage and outbound message quoting?
- Should morphology be first-class now or deferred to phase 2?
- Should Judaism pack ship with one default confession profile or multiple?
