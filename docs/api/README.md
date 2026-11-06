# OHS Platform API — OpenAPI 0.133.0

Owner: OHS platform team. Support: contact@opeshealthsystems.com. This versioned API exposes public enquiry and partner intake, localized service/product catalogues, developer OAuth and restricted machine-client lifecycle, caller-scoped usage/quota/metrics, and 645 role/scope/record-governed business workflows across 465 paths (656 HTTP operations). OpenAPI is `0.133.0`; `/api/v1/discovery` provides canonical contract, status, catalogue, changelog, SDK metadata and developer-portal links. The downloadable Node SDK preview is `0.146.0`, contains all 656 operation identifiers, and generates request and success-response contracts from OpenAPI. Its response coverage audit identifies 174 field-specific data schemas, 479 generic data shapes and three no-content results, including expense registers and commands, budget registers and commands, recurring-invoice templates, typed invoice commands, quote commands and quote line items, all 21 administrator form-reference reads, partner membership, developer machine-client lifecycle, administrator usage, and redacted audit-log detail alongside quote, customer-profile, practitioner course progress, lesson navigation, customer course learning and certificates, customer tickets with public replies, customer surveys and public knowledge base, licenses, invoices and addressed documents, customer dashboard and service-request options. The archive checksum is recorded in `/downloads/opes-api-sdk.json`; the package remains unpublished to a registry. The public API discovery document, changelog and static catalogues are cacheable for five minutes; locale-dependent responses vary on `Accept-Language`. Dynamic status, authenticated data, commands and errors remain `no-store`. The API is not an electronic medical record or clinical decision engine. Health Data and CDSS descriptions are service offerings for enquiry only. Material service gaps remain: isolated sandbox tenants, signed webhooks, organization-level tenancy, customer plans/billing, registry publication, and measured SLOs. These are not represented as available capabilities. Partner intake accepts health AI and academic collaborations but does not grant data access; any data access requires separate review and approval.

The localized API product catalogue at `GET /api/v1/api-products` lists product availability, authentication, scopes, access paths and data boundaries. Signed-in developers can manage their own public PKCE applications and restricted machine clients through `/en/developers/console` or `/fr/developers/console` and corresponding owner-scoped APIs. Machine clients are limited to `catalogue:read` and `integration:read`; they cannot access patient records or delegated business resources. Client secrets appear only at creation/rotation. Quotas can be configured by administrators, while legacy clients remain unlimited until a limit is set. Delegated users can inspect only their own activity, usage and metrics for the current OAuth application.

Business workflows include CRM and quote operations, support, workforce, contracts, assets, finance registers and reports, customer facilities, risk and performance management, validation, practitioner and recruitment workflows, learning, surveys, account recovery, protected admin references and audit-log reads. Finance and HR fields are returned through allowlisted resource projections. Partner members see only approved organizations linked to their account; organization-owner changes require idempotency and preserve at least one owner. Course assessments require enrollment, are scored server-side and reveal answers only after submission. Website-form delivery operations expose safe metadata and permit explicit reconciliation without exposing form contents or resending email.

The practitioner application payout command is available at `POST /api/v1/practitioners/applications/{application}/payout`. It requires an active administrator, the reviewed `practitioners:write` delegated scope, and an `Idempotency-Key`. A `202` means the request was durably accepted, not that settlement completed. Read the application resource for payout status. Provider outcomes that cannot be confirmed are held for reconciliation and are not automatically retried. Only MTN MoMo and manual settlement are available; Orange Money remains unimplemented.











Administrator-owned machine API clients can now be listed, created, rotated and revoked through `/api/v1/admin/api-clients`. These operations require an active administrator, delegated OAuth authorization and the `api-clients:manage` scope. Creation and rotation return a plaintext secret in a `Cache-Control: no-store` response. Replaying the same idempotency key and payload within its 24-hour retention window can return the same response; client lists and domain events contain no secret. Client-credentials clients remain limited to `catalogue:read` and `integration:read`.

Administrators can also register and revoke reviewed public OAuth applications through `/api/v1/admin/oauth-clients`. These authorization-code clients use exact HTTPS redirect URIs, user consent and S256 PKCE, and receive no client secret. The operations require an active administrator and delegated `oauth-clients:manage` scope.

Public product endpoints are `GET /api/v1/status` for a point-in-time application response and `GET /api/v1/changelog` for recent API release notes. Status does not probe dependencies or promise availability. Each endpoint is limited to 60 reads per minute per source IP.







