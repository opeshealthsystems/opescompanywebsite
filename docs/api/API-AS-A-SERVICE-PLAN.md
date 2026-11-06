# OPES Health API-first and API-as-a-service plan

**Review basis:** production OpenAPI `0.115.0`, downloadable SDK preview `0.111.0`, and the current registered workflow/adaptor audits.
**Latest production release:** `0.115.0` is live and verified. Developers can create, list, rotate and revoke up to five owner-scoped machine clients restricted to `catalogue:read` and `integration:read`; secrets are shown only once at creation or rotation, writes are idempotent and credential rotation/revocation invalidates existing tokens. The production contract and downloadable SDK checksum were checked after a scoped backup and release.

## Current production evidence

- Live OpenAPI `0.115.0` documents 644 registered business workflows across 462 paths (653 HTTP operations). It includes the localized Health Data Services and CDSS catalogue, health-AI and academic/research partner intake, user-owned OAuth application lifecycle and owner-managed public-scope machine clients. Live SDK preview `0.111.0` contains all generated operation identifiers; it remains unpublished to a package registry.
- The service catalogue introduced in `0.112.0` and partner application additions in `0.114.0` are shared by the API and website application workflows. Healthcare AI firms are invited as partners; OPES does not claim to build a healthcare AI model or offer unreviewed data access.
- Delegated OAuth applications use S256 PKCE, ownership checks, role-derived scopes, HTTPS or loopback callbacks, a five-active-app limit and token revocation on update/revoke. Mutations use idempotency. Developer-managed machine clients are separately limited to public catalogue and client-self-usage scopes; they cannot access patient records or delegated business resources.
- The bilingual Developer Console is available at `/en/developers/console` and `/fr/developers/console`. Signed-in developers can manage their public OAuth applications and machine clients through the same owner-scoped workflows offered by the API. Machine credentials are restricted to the public catalogue and the calling client’s own usage/quota. Live anonymous access redirects to sign-in; the authenticated production credential lifecycle was not exercised with a user account in this release. Usage remains aggregate rather than request-level analytics.
- Release `0.115.0` passes focused Laravel machine-client, OAuth-console and registry tests (7 tests, 6,515 assertions), all 15 SDK tests, OpenAPI 3.1.1 validation, and all 15 compatibility tests against the previously live `0.114.2` contract. Live checks confirmed OpenAPI `0.115.0`, 462 paths, 653 operations, all four machine-client operations, anonymous machine-client list HTTP 401, and SDK `0.111.0` size and SHA-256. This was a scoped deployment; the full platform suite was not run for this increment.
- Remaining API-as-a-service gaps include an isolated sandbox, registry-published SDK, signed webhooks, measured SLOs, organization tenancy, and customer-facing plans/billing. Signed-in users can self-manage up to five owner-scoped machine clients restricted to the public catalogue and each client's own usage/quota endpoints; this does not provide organization tenancy or a sandbox.
- Partner intake accepts the `health_ai` type and returns a minimal idempotent acknowledgement from the same application workflow used by the website. The public form collects company/contact details only; access to data remains subject to separate project review and approval.
- Localized website service, partner, solutions and Medical Informatics pages and the SDK checksum were checked. The production app remains in production mode, debug OFF and maintenance OFF; no database migration was needed for this release. The scoped pre-deploy backup is `/home/u117099711/domains/opeshealthsystems.com/backups/ohs-api-0.115.0-predeploy-20261106.tar.gz` (SHA-256 `0d295adc4bc881507af30bd467d7eedad871cb8beccf8537402aa715098d0ac0`).

## Prior production evidence (3 November 2026)

- The live site serves OpenAPI `0.110.0`: 454 paths, 641 HTTP operations, and 633 registered business workflows; `/me/usage` adds a delegated user and OAuth-application scoped 30-day usage read. The compatibility checker confirms all previously released `0.108.0` operation shapes are preserved except for the reviewed additive optional developer request schema; the OpenAPI 3.1 validator passes.
- The live Node SDK preview is `0.106.0` and contains all 641 generated operation identifiers. Its 23,596-byte archive SHA-256 matches live metadata. It remains unpublished to npm.
- The full Laravel suite passes on release `0.110.0`: 662 tests and 11,043 assertions. The SDK suite passes 11/11; OpenAPI 3.1 validation passes. The structured developer request form stores reviewable fields; its admin list/detail endpoints are role/scope protected. The SDK archive hash matches live metadata. Web and Filament audits report 61 named writes, 144 reads, 95 Filament read methods, 52 CreateRecord pages, 61 EditRecord pages and 159 files with custom Filament action callbacks, with no unmapped routes/handlers or direct Filament persistence calls detected. These source audits do not replace user/role-specific browser click-throughs or deployed evidence.
- A registry coverage test walks all 633 business workflows and verifies that each has a handler class/method, runtime API route, OpenAPI path/method, unique operation ID, required OAuth scope when private, and required idempotency header when mutating. This is a contract and wiring gate; it does not execute every business workflow with production-like data.
- User OAuth uses authorization code with S256 PKCE, consent, role-derived scopes and server-side checks. Machine clients use client credentials restricted to catalogue/integration reads. Public registration and password recovery use versioned endpoints and idempotency keys.
- The live Node SDK preview `0.106.0` covers all 641 operation identifiers but is not published to npm. There is no public sandbox, automatic public client registration, or organization tenancy. Named web business routes and audited Filament query/command entry points now share the application workflows with the API; runtime parity still needs role-specific browser verification.
- Live OpenAPI `0.110.0` adds delegated usage reads scoped to the authenticated user and OAuth application while preserving the released `0.109.0` operation shapes. The developer page loads its product-family cards from the public API product response. The additive lead JSON migration ran successfully in the 3 November release.

## Progress update — 2 November 2026

- Workforce employee reads now use `AdminEmployeeQueries`, separate from create/update commands. Both delegated employee read operations and the Filament employee table invoke that query handler; write responses reuse its employee field projection. This is an internal CQRS boundary correction with no HTTP contract change.
- Approved partner organization owners can list linked members, add existing active and verified partner accounts by email, and remove members. Each operation rechecks organization membership and approval; administrator membership management remains available. Writes require idempotency, produce versioned events and retain last-owner protection. The mobile organization workspace exposes these operations. Candidate OpenAPI 0.104.0 adds three operations; schema migration and live release remain pending.

- Branch candidate OpenAPI `0.100.0` exposes the caller's own 30-day UTC request-usage series through `GET /api/v1/integration/usage` with `integration:read`. The Node SDK adds `integrationUsage()` and prevents delegated-user clients from invoking machine-only operations. The meter remains client-scoped and reports authenticated machine-client attempts for the machine API routes only; it is not complete route analytics, user OAuth metering, quota billing, or production evidence until deployed and observed.
- Live OpenAPI `0.106.0` includes an administrator support report at `GET /api/v1/admin/api-clients/{client}/usage`, separated under the read-only `api-clients:read` scope. It provides per-client 30-day UTC totals and daily buckets without exposing secrets. Customer self-service usage remains a product gap.
- OAuth public-client provisioning and machine-client create/rotate/revoke/list CLI commands now dispatch through the same `WorkflowDispatcher` handlers as the API. Their handlers own validation, role checks, transaction/outbox behavior, and secret lifecycle; CLI code only resolves the administrator, presents outcomes, and writes one-time machine credentials to a mode-0600 private file. A CI source audit rejects direct client persistence in these CLI adapters. This closes an internal entry-point bypass, but customer self-service client registration remains unimplemented.
- Website-form delivery reconciliation is now a scoped API workflow: administrators can list and inspect metadata for email deliveries and confirm an `uncertain` event as received without resending. Responses omit form content. The CLI runs through these same query/command handlers; confirmation records a versioned audit event and still requires explicit `confirmed_received: true`. This exposes necessary operator recovery without exposing delivery records publicly.

- The mobile app now uses role-specific home screens and four persistent, role-adaptive tabs. Real accounts sign in through the registered public OAuth client using authorization-code PKCE; `/api/v1/me` roles and scopes select the workspace. The administrator sample profile has been removed.
- Customer service-request and CRM-deal forms now send the required API fields. Account recovery, notifications, profile actions, course progress and certificates have app screens backed by their published APIs where those operations exist.
- The live code-level web adapter audit reports 61 named mutations (52 shared application workflows, one shared application-service adapter, eight framework/protocol adapters) and 144 named reads (108 shared application queries, 36 content/view/transport adapters), with zero unmapped routes. This is evidence for the inspected web route boundary, not a claim that every platform capability is API-complete.
- The current branch's API contract CI job regenerates the OpenAPI contract and SDK operation-ID catalogue from the workflow registry and rejects checked-in drift; it also runs the named web-route adapter audit. This does not prove response-schema behavior or close the listed product gaps. The GitHub Actions run for the new usage endpoint exposed invalid Laravel slug-binding notation in older OpenAPI paths; the generator and compatibility checker now normalize it to the wire parameter form.
- Android preview OTA group `c0ec69f7-ce49-4c0a-bc2d-d8f683fb2d04` is published for runtime `0.1.0`. Build 6 completed on EAS from commit `4c6b851`; physical-device and real-account sign-in were not exercised.
- The machine API provides programmatic self-usage through `integration:read`, and administrators have a separate delegated support report. There is no customer-facing usage dashboard or request-level analytics. Other material gaps remain: isolated sandbox tenants and fictional test data, a registry-published SDK, customer organization ownership, signed webhooks, and measured plan/quota operations. Do not advertise these as shipped.

## What “API as a service” means for OPES

An API product gives a customer a supported way to discover, obtain access to, consume, observe and evolve API access. The endpoint catalogue is only one part. The service must also provide:

1. **A governed contract:** OpenAPI generated from the same workflow/resource definitions used at runtime, compatibility checks, stable release channels, deprecation notices and migration guides.
2. **A developer journey:** product catalogue, API reference, runnable examples, SDK installation, integration guide, client registration, credential lifecycle, support and an isolated sandbox.
3. **Access plans:** named consumer plans, approved products/scopes, quotas, rate limits, usage visibility and a clear upgrade/support path. Keep user OAuth separate from machine integration credentials.
4. **Operations:** versioned status, request IDs, latency/error and quota metrics, rate-limit headers, incident communication and published service objectives.
5. **Events and integrations:** documented domain events with authenticated, signed webhook subscriptions, delivery attempts, retries, replay and subscription controls. The internal outbox is a useful foundation; it is not itself a customer webhook service.
6. **Security and governance:** least privilege, active-role and record authorization, credential rotation/revocation, audit trails, data minimization, retention, consent and tenant ownership before organization-wide data access is promised.

## API product families to package

These are capability boundaries, not committed price tiers. Package entitlements and pricing only after demand, cost-to-serve and support capacity are measured.

- **Catalogue and enquiry:** published product metadata, pricing enquiry submission and integration status.
- **Customer operations:** facility profile, service requests, support and account records for an authorized customer organization.
- **Practitioner learning:** course library, lessons, enrollment/progress, certificates and assessments. Live OpenAPI 0.106.0 includes timed attempts, server scoring and post-submission answer review; migrations and release are complete.
- **Business operations:** CRM, quotes, partner applications and administrative workflows behind delegated user OAuth and role/scope checks.
- **System integration:** reviewed machine clients for explicitly approved service-to-service scopes. User records stay on user OAuth; machine credentials never inherit a person's dashboard.

The live developer portal now loads API family, preview status, authentication method, scope, access path and data boundary from `GET /api/v1/api-products`. Customer registration, plan limits, sandbox support, service history and consumer usage dashboards remain separate work; the catalogue does not imply those services exist.

## Delivery sequence

### 1. Finish API-first parity

- Inventory every web, Filament, mobile and scheduled workflow against a named application query/command and contract operation.
- Move remaining business rules out of controllers, UI components and Eloquent-only branches into context application handlers. Web and mobile should call those handlers through adapters; first-party server-rendered pages need not make a network call back into their own server.
- Continue gap analysis for real mobile needs. Partner organization membership/profile and practitioner assessment workflows are now live; add further operations only where the owning domain and record rules are defined.
- Give every UI action a permission, endpoint, request schema, response state and error/empty state. Hide unavailable operations rather than simulating success.

### 2. Make the contract and SDK releasable

- Define and document the 1.0 stability bar for the existing `/api/v1` surface, including support duration, deprecation windows and compatibility guarantees.
- Generate OpenAPI, examples and the Node SDK from the same source definitions in CI; reject drift between the deployed spec, packaged SDK and operation registry.
- Publish the SDK to an approved package registry with semantic versions, provenance/checksums, release notes and a support policy. Keep downloadable archives as an additional channel, not the only release path.
- Prioritize complete examples, pagination/filter conventions and consistent error examples. Do not bulk-rename existing snake-case wire fields; make any future naming change additively and version it.

### 3. Operate API plans as a service

- Continue the customer-facing developer portal. Product discovery, user-owned public OAuth application management and restricted machine-client lifecycle are live. Remaining portal work includes organization-level ownership/governance, richer usage analytics, plan/quota presentation and sandbox access.
- Offer separate sandbox tenants and test data. Ensure sandbox credentials and records cannot reach production accounts or send production email.
- Define quotas and plans before charging: per-client and per-user consumption, endpoint family, monthly allowances, overage policy and alerts. Publish the counters and rate-limit headers before selling a quota.
- Add customer-visible usage analytics, request IDs, status/incident history and support escalation. Establish service objectives from measured traffic before advertising an SLA.
- Add signed webhooks for selected domain events after subscription authorization, delivery persistence, retry/backoff, replay protection and secret rotation are in place.

## Product boundaries to keep explicit

| Capability | Current contract evidence | Product gap |
| --- | --- | --- |
| Public catalogue and enquiries | Versioned reads/intake, status, changelog and localized API product catalogue | Isolated sandbox, service-level analytics and support commitments |
| Machine integrations | Client credentials, restricted catalogue/integration scopes, client-scoped usage, administrator support usage, and owner-managed create/list/rotate/revoke | Organization ownership, customer plan/limit management, sandbox credentials and public SDK registry release |
| User-connected apps | PKCE, consent, `/me`, effective role/scopes, and user-owned public OAuth application lifecycle | Organization-level application ownership and broader tenant-aware consent |
| First-party web/Filament | Named web routes and scheduled jobs delegate through shared handlers; Filament read and create/edit adapters are audited in CI | Verify full role-by-role rendered click-through and any unregistered Livewire/background integrations |
| Practitioner learning | Course, lesson completion, progress, certificates and enrollment-checked assessments | Continue role-specific user journey verification; course certificates follow the existing lesson-completion policy. |
| Partner accounts | Public directory and application intake, approved-account membership, owner/member roles, and partner-scoped profile and member management | Continue organization-level tenancy and partner lifecycle review. |

## Progress update — 3 November 2026

- Candidate `0.106.0` adds admin-only delivery queries at `GET /api/v1/admin/enquiries/deliveries` and `/{event}`, plus idempotent confirmed-receipt reconciliation at `POST /api/v1/admin/enquiries/deliveries/{event}/resolve`. The API returns allowlisted operational metadata only, and only `uncertain` events can be marked received. No email is resent; a versioned audit event is recorded.
- The legacy resolution CLI command now calls these same query/command handlers. OAuth client provisioning/management commands also use the shared identity workflow handlers. CI source audits guard both classes of CLI adapters from direct persistence.
- OpenAPI 3.1 validation and compatibility against the last committed `0.100.0` contract pass. The SDK preview is `0.102.0`, covers all 636 operations, and has a checksum-verified 23,090-byte archive. SDK tests pass 11/11; focused delivery API and CLI tests pass. The full Laravel suite passes 656 tests and 10,950 assertions.
- Deployed to production at `opeshealthsystems.com` on 3 November 2026. Live checks returned OpenAPI `0.106.0`, 450 paths, `/en` and `/en/developers` at 200, the SDK preview checksum matching live metadata, and 401 for anonymous delivery, partner, and practitioner-assessment APIs. Both migrations are recorded `Ran`; the public SDK remains unpublished.

## Progress update — API product discovery, 3 November 2026

- Added `GET /api/v1/api-products` as a public read operation in the shared API workflow registry. It publishes four accurately bounded API families, their preview status, authentication model, scope, access paths and data boundaries. English and French labels use the standard `Accept-Language` selection.
- The developer page fetches this endpoint and renders the product cards from its response. API product detail is now sourced from the same versioned contract rather than a second hard-coded page list.
- OpenAPI 0.107.0 and downloadable Node SDK preview 0.103.0 are live. The release includes 630 business workflows over 451 paths (637 HTTP operations); all 0.106.0 operation shapes are preserved. The SDK archive was downloaded from production and matched its published SHA-256 metadata.
- Remaining API-as-a-service gaps are customer-managed app registration, a segregated sandbox, consumer-facing usage and quota plans, signed webhooks, npm publication, and measured support/SLO commitments. These remain clearly labeled as unavailable.

## Release gate for each conversion

Ship only when the workflow has an API contract and operation ID, validated role/scope and record authorization, idempotency for retryable commands, structured errors/request IDs, OpenAPI/SDK parity, and a real UI/API click-through. For external integrations, also verify quota headers, audit events, usage metrics and the supported SDK release. A large API catalogue alone does not make the whole platform API-first or an API service.

## Progress update — delegated API usage visibility, 3 November 2026

Live OpenAPI 0.110.0 adds `GET /api/v1/me/usage`. Authentication records UTC daily request totals by delegated user and OAuth application; the read endpoint returns only the signed-in user's totals for the current application and requires `identity:read`. This adds privacy-scoped usage visibility without defining prices, quotas, or billing policy. Production reports 454 paths and 641 HTTP operations; SDK preview 0.106.0 is generated from the same contract. The migration is applied and live verification succeeded on 3 November 2026.
