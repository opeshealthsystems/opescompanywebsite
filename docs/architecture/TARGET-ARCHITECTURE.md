# OHS target architecture

Date: 30 October 2026. Status: target design recorded at the owner's direction; implementation is progressing incrementally. See [IMPLEMENTATION-STATUS.md](IMPLEMENTATION-STATUS.md) for current code and deployment evidence. This target does not claim that the existing application already implements every capability.

## Mandatory foundation

OHS will be API-first and its external APIs will be managed as products. Every new business capability must have a reviewed machine-readable contract, a named owner and authorization rules before implementation. Web, mobile and integration clients use the same application use cases. No essential workflow may exist only inside a Blade page or Filament action.

Begin as a Laravel modular monolith with explicit domain boundaries and a relational database. Separate deployment into microservices requires a demonstrated scaling, reliability or ownership need. API-first does not require microservices. Existing web routes remain supported during incremental migration.

## API as a product

Each published API product must define its consumer, supported use cases, owner, scopes, resource boundaries, environments, onboarding, examples, support route, lifecycle and measurable service objectives. Public documentation must distinguish experimental, preview, supported and deprecated capabilities.

The developer experience includes an API catalogue, searchable reference, quickstart, sandbox with fictional data, application registration, credential rotation/revocation, usage visibility, changelog and status information. Enable quotas and usage metering by consumer/application. Paid plans and billing are optional business decisions; do not invent pricing or uptime guarantees.

Maintain separate public, partner and internal exposure policies. An internal endpoint is not automatically a supported external API. Staff payroll, application reviews and private documents must never become public merely because the platform is API-first.

## Contracts and HTTP conventions

- Author OpenAPI contracts before endpoint code. Use an explicitly pinned OpenAPI version supported by the selected validator, documentation and client-generation tools; record and test that choice before the first API release.
- Start with same-origin `/api/v1`; a separate API hostname requires a separate deployment/DNS decision. Locale is a representation preference, not an API version.
- Describe operation IDs, request/response schemas, examples, security requirements, pagination, filters, limits and every expected error. Whitelist response fields; never serialize entire Eloquent models as the public contract.
- Use correct HTTP methods/statuses and `application/problem+json` errors with stable problem types, correlation IDs and safe field-validation details. Never return stack traces or credentials.
- Require bounded pagination and allowlisted filters/sorts. Specify UTC timestamps, stable opaque resource IDs and currency-aware monetary representation. IDs do not replace authorization.
- For retryable creation/payment operations define `Idempotency-Key`: scope by consumer, principal and operation; persist request fingerprint and result; reject different payloads using the same key. Publish retention/replay semantics. Use transactions and uniqueness constraints for concurrent duplicates.
- Use ETags/version preconditions where concurrent edits can lose data. Define consistent `401`, `403`, `404`, `409`, `412`, `422` and `429` behavior, including rate-limit retry guidance.
- Prefer additive changes. Breaking changes require a new major API version, migration guide, announced retirement date and consumer impact review. Sunset periods and support promises require an approved product policy.
- CI must validate/lint contracts, detect breaking changes and exercise implementation against schemas. Generated SDKs and examples must pass a real sandbox smoke test.

## Identity and authorization

Interpret the requested authentication architecture as OAuth 2.0 for delegated API access and OpenID Connect for federated sign-in. Auth0 is a possible identity provider, not a required synonym for OAuth and not yet selected. Select an OIDC-capable provider or a maintained authorization-server solution after checking requirements, hosting compatibility, cost and lifecycle support. Do not build custom token cryptography.

Use authorization code with PKCE for interactive delegated clients and client credentials for approved machine clients. Do not use the implicit flow or resource-owner password grant. Validate issuer, audience, signature, expiry and granted scopes; rotate keys and support revocation. Keep browser tokens out of persistent JavaScript storage where a backend-for-frontend and secure session cookie are appropriate. Existing first-party sessions may continue with CSRF protection.

Scopes express capabilities such as `catalogue:read` or `enquiries:write`; server policies additionally enforce actor, organization, record ownership and field-level access. A scope alone never authorizes every record. Machine clients do not impersonate arbitrary users. Require MFA for privileged administration and audit sensitive changes without recording secrets.

Define organization ownership before exposing organization-scoped APIs. Existing data must be mapped and reviewed; do not assign all legacy records to a guessed tenant. Add negative tests for cross-organization access, privilege escalation, token replay and revoked clients.

## Domain-driven boundaries

These are initial bounded-context candidates grounded in existing models, to be validated against actual business workflows:

| Context | Existing concepts | Ownership and first API use cases |
| --- | --- | --- |
| Identity and access | User, roles, policies | Principals, memberships, permitted capabilities |
| Catalogue and publishing | Product, BlogPost, Course | Published content and product discovery |
| Enquiries and partnerships | Lead, DemoRequest, PartnerApplication | Submit, acknowledge and manage enquiries |
| Practitioner and testing programmes | PractitionerApplication, TesterApplication, assignments, findings | Applications, assignment and review workflows |
| Customer delivery and support | Project, ServiceRequest, Ticket | Customer requests, delivery progress and support |
| Commercial and finance | Quote, Invoice, InvoicePayment, payouts | Quote/invoice lifecycle and authorized payment operations |
| Workforce | EmployeeProfile, LeaveRequest, PayrollRun | Restricted staff workflows |
| Documents and assurance | Document, certificates, validation workflows | Controlled signing, review and evidence access |

Each context owns its writes, invariants and application services. Other contexts call explicit interfaces or consume documented events rather than modifying its tables. Shared database deployment is initially acceptable; ownership still applies. Keep shared code small. Clinical capabilities advertised as separate products are not assumed to exist in this repository.

Suggested structure for each migrated context:

```text
app/Contexts/Enquiries/
  Domain/          # business rules, state transitions, domain events
  Application/     # command/query handlers, authorization orchestration, ports
  Infrastructure/  # Eloquent persistence, mail and outbox adapters
  Interfaces/Http/ # API controllers, requests, resources
```

Dependencies point toward domain/application abstractions. Domain rules must not depend on HTTP, Blade, Filament or external mail providers. Use rich aggregates for meaningful invariants; ordinary CRUD does not need a complicated aggregate hierarchy. Migrate existing models behind adapters as workflows are extracted.

## CQRS where useful

Separate write use cases from read use cases at the application layer. For example, `SubmitEnquiry` validates policy and creates the submission, while `ListEnquiries` returns an authorized projection. Web and API entry points invoke those same handlers; the web backend need not make loopback HTTP calls to itself.

Initially both paths use the same database and transactional consistency. Introduce separate read projections only for demonstrated reporting or performance needs. Document projection lag, access controls, rebuild procedure and read-after-write behavior. Complex programme reviews, invoice transitions and payout execution benefit from explicit commands; simple content retrieval remains simple. CQRS does not require event sourcing or separate databases.

## Events and reliable effects

Use domain events for meaningful completed transitions and versioned integration events for cross-context/consumer communication. Publish only after durable state changes. Store the business write and an outbox record in the same database transaction. A supervised worker publishes outbox records; consumers deduplicate by event ID and retain a processing ledger. Delivery is at least once, not a promise of exactly-once email or payment execution.

An integration envelope includes event ID, type, schema version, occurrence time, producer, aggregate ID/version, correlation/causation IDs and organization identifier when applicable. Minimize payloads and exclude secrets. Define ordering per aggregate, retry/backoff, dead-letter handling, replay permissions and schema compatibility. Replays must not silently repeat email or financial effects.

For partner webhooks use per-subscription signing secrets, timestamped signatures, replay protection, rotation and an inspectable delivery history. Validate destinations against SSRF, restrict redirects, enforce HTTPS and allow consumers to retry safely. Webhook delivery failure does not roll back an already committed business operation.

Do not introduce event sourcing initially. Add it only for a specific justified domain with explicit retention, correction and reconstruction requirements.

## First migration: website enquiries

1. Contract the public enquiry endpoint and protected staff retrieval separately. Public submission must not expose enquiry lists or accept arbitrary recipient/owner fields. Apply spam controls and throttling; document validation and acknowledgement behavior.
2. Extract `SubmitEnquiry` and reuse it from the existing contact/home/product entry points and the versioned API. Preserve locale, source, requester reply-to and the fixed destination `contact@opeshealthsystems.com`.
3. Commit the enquiry and delivery intent together. Attempt delivery immediately after commit; keep a durable outbox fallback. Return a truthful recorded/pending/sent state rather than implying SMTP acceptance when it failed.
4. Import existing private delivery-ledger successes using stable source IDs so historical submissions are not resent. Reconcile counts before cutover. Retain the ledger until reconciliation and rollback are proven.
5. Enable monitored retries only after hosting supports a continuously supervised worker or reliable scheduler. Current hosting has no verified retry scheduler; do not claim background delivery until it has passed failure/recovery tests.
6. Prove one submission per idempotency key, correct recipient, safe reply-to, schema compliance, authorization, failure recovery and equivalent web/API behavior. Then migrate demo, partner and tester forms.

The currently deployed synchronous mail path remains the operational baseline until the replacement meets these gates.

## Operations and security

Before publishing partner access establish supported runtime/worker hosting, database backups with a restore drill, private secret management, HTTPS, request/body limits, per-consumer quotas, structured redacted logs and correlation IDs. Measure API error rates/latency, authentication denials, outbox age, retry backlog and webhook failures. Set service objectives from measured capacity and business needs.

Apply data minimization, retention and deletion rules to primary records, events, logs, backups and sandboxes. Do not include real customer or health data in developer examples. Audit privileged access. Threat-model object authorization, bulk export, uploads, signing links, credential leakage and webhook destinations before external release.

## Delivery sequence and release gates

| Phase | Deliverable | Required evidence |
| --- | --- | --- |
| 0: baseline | Inventory web workflows, consumers, data ownership and API products | Approved exposure matrix; rollback and backup verified |
| 1: API foundation | Contracts, versioned routing, error format, policies, rate limits and CI checks | Contract, negative-authorization and compatibility tests |
| 2: first vertical slice | Enquiry command/query handlers, web adapter, API and durable delivery | Live controlled submission, ledger reconciliation, worker failure/retry and rollback tests |
| 3: identity and developer experience | Selected OAuth/OIDC integration, sandbox, registration, credentials and reference docs | PKCE/client-credential tests, revocation/MFA tests and independent sandbox quickstart |
| 4: domain expansion | Catalogue, applications, support and commercial APIs in reviewed increments | Per-context ownership and migration tests; no broken existing workflows |
| 5: partner launch | Supported API catalogue, quotas, usage, support policy and webhooks | Consumer acceptance, load/failure exercises, restore drill and operational sign-off |

Identity is a dependency of every protected API: no protected phase-1/2 endpoint is externally released before the phase-3 identity gates pass. Public enquiry submission can be delivered independently with abuse controls. Finance/workforce access requires its own risk and authorization review.

A workflow is migrated only when its contract, implementation, tests, documentation, operational monitoring and backward-compatible web behavior are complete. Publishing a document or OpenAPI file alone is not an API-first implementation. No domain, DNS, account relinking or destructive data migration is authorized by this design document.

## Standards references

- [OpenAPI specification](https://spec.openapis.org/oas/): machine-readable HTTP API contracts; toolchain version must be pinned during implementation.
- [OAuth 2.0 Security Best Current Practice, RFC 9700](https://www.rfc-editor.org/rfc/rfc9700.html): baseline for delegated authorization flow security.
- [HTTP API Problem Details, RFC 9457](https://www.rfc-editor.org/rfc/rfc9457.html): structured error responses.
