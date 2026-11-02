# OPES Health API-first and API-as-a-service plan

**Review basis:** local and live OpenAPI contract `0.99.1`, inspected 1 November 2026. This document distinguishes shipped API capabilities from the remaining service product work.

## Current evidence

- The live site and repository both serve OpenAPI `0.99.1`: 431 paths and 611 operations.
- The API design linter reports 611 operations, 0 errors, 379 warnings and 845 informational findings. Every warning is the linter's preference for camelCase/kebab-case over the platform's established `snake_case` query names; do not rename those in place because that would break clients.
- The API scorecard is **B / 81.36**. Contract documentation scores A / 90.25 and security B / 85. The heuristic rates usability D / 68.73 and performance D / 61.53, chiefly recommending stronger onboarding/examples, cache strategy and field selection.
- Comparing the `0.94.0` live baseline with `0.99.1` finds 17 additions or changes, no confirmed removals, and one potential security-scheme change. Manual comparison shows the OAuth endpoints are unchanged; two management scopes were added. Keep the finding in the compatibility review until the release gate verifies generated clients and consumers.
- User OAuth uses authorization code with S256 PKCE, consent, role-derived scopes and server-side checks. Machine clients use client credentials restricted to catalogue/integration reads. Public registration and password recovery use versioned endpoints and idempotency keys.
- The Node SDK is downloadable as a preview and covers the operation identifiers, but it is not published to npm. Developer docs explicitly say there is no public sandbox, automatic client registration, organization tenancy, or full API conversion of every internal/admin action.
- The live app has role-scoped API reads and writes for supported workspaces. Five explicitly labelled fictional profiles (staff, customer, practitioner, tester and partner) use local sample records only; there is no administrator demo. Partner private self-service is absent from the API contract. Course lesson/progress APIs exist; an exams/assessment API is not currently published.

## Progress update — 2 November 2026

- The mobile app now uses role-specific home screens and four persistent, role-adaptive tabs. Real accounts sign in through the registered public OAuth client using authorization-code PKCE; `/api/v1/me` roles and scopes select the workspace. The administrator sample profile has been removed.
- Customer service-request and CRM-deal forms now send the required API fields. Account recovery, notifications, profile actions, course progress and certificates have app screens backed by their published APIs where those operations exist.
- The live code-level web adapter audit reports 61 named mutations (52 shared application workflows, one shared application-service adapter, eight framework/protocol adapters) and 144 named reads (108 shared application queries, 36 content/view/transport adapters), with zero unmapped routes. This is evidence for the inspected web route boundary, not a claim that every platform capability is API-complete.
- The current branch's API contract CI job regenerates the OpenAPI contract and SDK operation-ID catalogue from the workflow registry and rejects checked-in drift; it also runs the named web-route adapter audit. This does not prove response-schema behavior or close the listed product gaps. The updated GitHub Actions workflow still needs to run.
- Android preview OTA group `c0ec69f7-ce49-4c0a-bc2d-d8f683fb2d04` is published for runtime `0.1.0`. Build 6 completed on EAS from commit `4c6b851`; physical-device and real-account sign-in were not exercised.
- The remaining API product work is still material: isolated sandbox tenants and fictional test data, a registry-published SDK, customer-visible integration usage and support tooling, organization ownership, private partner-account APIs, and exams/assessment APIs. Do not advertise these as shipped.

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
- **Practitioner learning:** course library, lessons, enrollment/progress and certificates. Exams are a separate product capability after assessment APIs ship.
- **Business operations:** CRM, quotes, partner applications and administrative workflows behind delegated user OAuth and role/scope checks.
- **System integration:** reviewed machine clients for explicitly approved service-to-service scopes. User records stay on user OAuth; machine credentials never inherit a person's dashboard.

The developer portal should let a prospective consumer see which family is available, its data boundary, access method, limits, support level and maturity status before requesting access.

## Delivery sequence

### 1. Finish API-first parity

- Inventory every web, Filament, mobile and scheduled workflow against a named application query/command and contract operation.
- Move remaining business rules out of controllers, UI components and Eloquent-only branches into context application handlers. Web and mobile should call those handlers through adapters; first-party server-rendered pages need not make a network call back into their own server.
- Add explicit API operations for each real mobile need, including partner self-service and exams only after their domain rules and ownership are defined.
- Give every UI action a permission, endpoint, request schema, response state and error/empty state. Hide unavailable operations rather than simulating success.

### 2. Make the contract and SDK releasable

- Pick and document the 1.0 stability bar for the existing `/api/v1` surface. Resolve the one compatibility-review finding and publish the additive scope change in the migration notes.
- Generate OpenAPI, examples and the Node SDK from the same source definitions in CI; reject drift between the deployed spec, packaged SDK and operation registry.
- Publish the SDK to an approved package registry with semantic versions, provenance/checksums, release notes and a support policy. Keep downloadable archives as an additional channel, not the only release path.
- Prioritize complete examples, pagination/filter conventions and consistent error examples. Do not bulk-rename existing snake-case wire fields; make any future naming change additively and version it.

### 3. Operate API plans as a service

- Launch a customer-facing developer portal with product tiers, application registration requests, callback/scope review, credential lifecycle, quick starts and integration support.
- Offer separate sandbox tenants and test data. Ensure sandbox credentials and records cannot reach production accounts or send production email.
- Define quotas and plans before charging: per-client and per-user consumption, endpoint family, monthly allowances, overage policy and alerts. Publish the counters and rate-limit headers before selling a quota.
- Add customer-visible usage analytics, request IDs, status/incident history and support escalation. Establish service objectives from measured traffic before advertising an SLA.
- Add signed webhooks for selected domain events after subscription authorization, delivery persistence, retry/backoff, replay protection and secret rotation are in place.

## Product boundaries to keep explicit

| Capability | Current contract evidence | Product gap |
| --- | --- | --- |
| Public catalogue and enquiries | Versioned reads/intake, OpenAPI, status and changelog | Sandbox, conversion/usage analytics and support service |
| Machine integrations | Client credentials and restricted catalogue/integration scopes | Customer self-service onboarding, plan quotas and public SDK release |
| User-connected apps | PKCE, consent, `/me`, effective role/scopes | Broader tenant-aware consent and customer-facing client governance |
| First-party web/Filament | Many operations share application workflows | Finish parity inventory for every UI and background workflow |
| Practitioner learning | Course, lesson completion, progress and certificates | Exams require assessment model/API before app exams are real |
| Partner accounts | Public directory and application intake | Private partner profile, organization membership and account APIs |

## Release gate for each conversion

Ship only when the workflow has an API contract and operation ID, validated role/scope and record authorization, idempotency for retryable commands, structured errors/request IDs, OpenAPI/SDK parity, and a real UI/API click-through. For external integrations, also verify quota headers, audit events, usage metrics and the supported SDK release. A large API catalogue alone does not make the whole platform API-first or an API service.
