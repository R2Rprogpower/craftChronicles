# Architecture Context

## Existing conventions

The reference modules (`Users`, `Auth`, `Permissions`) use:

1. thin HTTP controllers;
2. Form Requests for authorization and validation;
3. Processors as use-case entry points;
4. DTOs at write boundaries;
5. Services for domain/application rules;
6. Repositories for persistence queries;
7. Presentations for response shaping;
8. module-local routes, migrations, factories, and seeders;
9. shared Eloquent models under `App\Models`;
10. auto-loaded module migrations and auto-discovered seeders.

The new code follows this vocabulary. No new container, event bus, or generic
repository abstraction was introduced.

## New bounded contexts

- `PersonalBrand`: public page orchestration and central brand profile.
- `Portfolio`: extensible portfolio/case-study records.
- `Services`: configurable service catalogue.
- `Content`: future channel-agnostic content pipeline.
- `Products`: product lifecycle and roadmap foundation.
- `BrandProgress`: execution areas and relational milestones.
- `ServiceRequests`: public sales lead write flow.

Only `PersonalBrand` owns cross-context reads, because the public portfolio is a
composed read model. Operational writes belong to their module; `ServiceRequests`
implements the complete write pipeline.

## Page delivery

```text
GET /portfolio or /progress
  -> BrandPlatformController
  -> route-specific Processor
  -> BrandPlatformService
  -> BrandPlatformRepository (composed read model)
  -> BrandPlatformPresentation
  -> Blade mount + serialized payload
  -> React page and reusable sections
```

## Service request delivery

```text
React modal
  -> POST /service-requests (CSRF + throttle)
  -> StoreServiceRequestRequest
  -> ServiceRequestStoreProcessor
  -> CreateServiceRequestDTO
  -> ServiceRequestService
  -> ServiceRequestRepository
  -> service_requests
  -> SuccessResponse (201)
```

## Extension rules

- Add mutable brand content to a table/structured field before adding JSX literals.
- Promote JSON structures to related tables when they need independent identity,
  filtering, lifecycle, or editing permissions.
- Keep page components compositional; domain mapping stays server-side.
- Add notifications after the service request transaction, preferably queued.
- Future private dashboard authorization should wrap `/progress`; do not fork it.
