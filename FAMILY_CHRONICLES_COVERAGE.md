# Family Chronicles coverage matrix

This file records the honest scope of the lightweight MVP derived from the long-form Living Biography / Family Chronicles specification.

| Area | Status | Notes |
| --- | --- | --- |
| Existing Craft Chronicles integration | Complete | Isolated module and `/family-chronicles` URL. |
| Existing authentication | Complete | All module routes require `auth`. |
| Multiple independent users | Complete | Owner-scoped people, answers, daily questions, and files. |
| Multiple people per user | Complete | Add and switch people. |
| Question bank | MVP complete | 183 stable Russian questions, 15 categories, 11 life stages. |
| Question of the day | Complete | Stable by user/person/date and prefers unanswered questions. |
| Story mode | MVP complete | Chronological filtering and next navigation. |
| Random mode | Complete | Uses unanswered candidates first. |
| Category and age filters | Complete | Combined filters supported. |
| Future-stage suppression | Complete | Uses optional birth year. |
| Text answers | Complete | Long text, edit, autosave, local recovery. |
| Answer states and progress | Complete | Draft/answered/deferred/skipped and progress count. |
| Existing file upload | MVP complete | Private photos/audio/video/PDF, max 50 MB each. |
| Browser audio/video recording | Deferred | Use file upload in MVP. |
| Resumable/chunked upload | Deferred | Manual retry after upload failure. |
| Accessibility | MVP complete | Large type, keyboard-native controls, labels, responsive layout. Formal WCAG audit deferred. |
| Localization | Foundation only | Locale stored; Russian UI/data only. |
| Family graph and relationships | Deferred | Requires a separate data-model phase. |
| Conflicting testimonies and sources | Deferred | Current answers are one record per person/question. |
| Medical history | Deferred | Requires dedicated consent and privacy design. |
| GEDCOM/import/export | Deferred | No export in MVP. |
| Biography/book generation | Deferred | No narrative compilation in MVP. |
| Notifications | Deferred | Daily question is shown on visit only. |
| AI/transcription | Intentionally omitted | Avoids external disclosure, cost, and extra infrastructure. |
| Fine-grained sharing | Deferred | Private owner-only model is safer for first release. |
| Automated tests | Complete for MVP | Authentication, isolation, persistence, daily question, files, question rules. |
| Deployment documentation | Complete | See `docs/12-family-chronicles-deploy.md`. |

## Next recommended slice

After manual MVP testing, the highest-value follow-up is answer history plus ZIP/JSON export. This improves preservation and portability before adding AI or complex family collaboration.
