# OHS Platform API — preview 0.38.0

Owner: OHS platform team. Support: contact@opeshealthsystems.com. Purpose: provide stable, documented access to public enquiry intake, the published product catalogue, integration identity and delegated business workflows. OpenAPI `0.38.0` documents 340 business workflow operations across 272 paths (346 total HTTP operations). The platform conversion remains in progress; remaining admin actions and lifecycle hooks are tracked in the implementation status document.

Contract: [openapi.json](openapi.json), pinned to OpenAPI 3.1.1. Runtime reference: `/api/v1/openapi.json`. Production base URL: `https://opeshealthsystems.com/api/v1`. No sandbox environment is provisioned yet.

## Submit an enquiry

```http
POST /api/v1/enquiries
Content-Type: application/json
Accept: application/json
Idempotency-Key: 83a4e022-3d18-462d-aade-a07e70a39346

{"name":"Example sender","email":"sender@example.test","message":"Please share product information.","locale":"en"}
```

Do not run example submissions against production casually: a successful request creates a real enquiry and emails the contact mailbox. Use a local test database and fake mail for development.

Successful response is HTTP 201 with `data.status` equal to `recorded` and `data.delivery_status` equal to `sent` or `pending`. Sent means the mail transport accepted delivery, not confirmed inbox placement. Pending means the submission and its delivery intent are durably stored. Ambiguous SMTP outcomes are held for review. A minute-based recovery task is defined, but requires hosting cron activation; it is not yet running automatically.

The endpoint accepts JSON, limits input fields/lengths, and permits five attempts per minute and thirty per day per source IP, including invalid attempts. Respect `Retry-After` on HTTP 429. Error bodies use `application/problem+json`; all responses carry a server-generated `X-Request-ID`. Share this identifier with support rather than including private enquiry contents.

This operation is deliberately anonymous and write-only. It exposes no lists, private submission records, admin functions or OAuth-protected capabilities. Unknown fields, including caller-supplied recipients, roles, source and status, are rejected.

Use a fresh random `Idempotency-Key` for each intended enquiry and retain that key for retries. The header is optional for compatibility, accepts 16–128 ASCII letters/digits or `._:-`, and is globally scoped to this anonymous endpoint. Same normalized input/key replays the saved 201 response for 24 hours with `Idempotency-Replayed: true`; changed input returns 409. A simultaneous request still being processed returns 409 with `Retry-After: 2`. Retrying after key expiry can create another enquiry. Without a key, requests are not deduplicated. Never use the static example key for real submissions.

The saved response is the original acknowledgement, not a live delivery-status query. Default `en` and omitted/null optional fields normalize equivalently. Validation failures do not reserve a key. Raw keys and enquiry bodies are not copied into the receipt table; fingerprints use HMAC with the application key. Rotating that key during the 24-hour window invalidates payload comparisons for existing receipts and should be coordinated with API consumers.

All four business enquiry forms now commit their contact-mail intent in the same transaction as the submission. The existing contact form and API share an application handler; other forms retain their interfaces and acknowledgements. Unique source/recipient records and atomic delivery claims prevent concurrent recovery workers from sending the same eligible record. An interrupted SMTP attempt is marked uncertain instead of silently reclaimed. This is not an exactly-once SMTP guarantee.

OAuth 2.0 client credentials is available for approved machine integrations, while business workflows use delegated OAuth authorization code with S256 PKCE and explicit user consent: see [identity operations](IDENTITY-OPERATIONS.md). Protected routes and scopes are defined in OpenAPI and the bilingual `/en/developers` and `/fr/developers` pages. The [server-side Node SDK](SDK-RELEASE.md) includes a generated operation-ID client for delegated business calls and a downloadable archive with checksum. OIDC, public consumer registration, a metering dashboard and a sandbox remain pending. See [delivery operations](DELIVERY-OPERATIONS.md) for recovery and hosting setup.

## Verification

The feature suite covers web and API email behavior, validation, field injection, write-only access, rate limiting, safe errors and SMTP failure recovery. CI validates the OpenAPI document using `openapi-spec-validator==0.7.2` and runs the enquiry/mail regression suites. CI must run on the remote before a remote passing status can be claimed. Breaking-change detection and generated-client checks are subsequent gates, not implemented by this workflow.

Standards: [OpenAPI 3.1.1](https://spec.openapis.org/oas/v3.1.1.html), [HTTP Problem Details](https://www.rfc-editor.org/rfc/rfc9457.html).


