# OHS Platform API — preview 0.99.0

Owner: OHS platform team. Support: contact@opeshealthsystems.com. Purpose: documented access to public enquiry intake, partner and tester application administration, partner-institution and practitioner-programme catalogues, support service requests, published content, CRM, role-scoped operations and workforce workflows, contract and asset records, finance registers and reports, customer facility profiles, employee and HR administration, risk and performance management, validation, practitioner, recruitment, learning, surveys, account recovery, machine catalogue access, protected admin references, immutable audit-log search and detail, resource summaries, finance line items, credit notes, and user and role administration. OpenAPI `0.99.0` documents 605 business operations across 431 paths (611 total HTTP operations); downloadable Node SDK `0.95.0` contains all 611 generated operation identifiers. The contract describes operation intent, authorization, pagination and idempotency requirements, with request and response-envelope examples. Private operations require current role and scope authorization. The SDK remains a downloadable preview and has not been published to a package registry.

The practitioner application payout command is available at `POST /api/v1/practitioners/applications/{application}/payout`. It requires an active administrator, the reviewed `practitioners:write` delegated scope, and an `Idempotency-Key`. A `202` means the request was durably accepted, not that settlement completed. Read the application resource for payout status. Provider outcomes that cannot be confirmed are held for reconciliation and are not automatically retried. Only MTN MoMo and manual settlement are available; Orange Money remains unimplemented.











Administrator-owned machine API clients can now be listed, created, rotated and revoked through `/api/v1/admin/api-clients`. These operations require an active administrator, delegated OAuth authorization and the `api-clients:manage` scope. Creation and rotation return a plaintext secret in a `Cache-Control: no-store` response. Replaying the same idempotency key and payload within its 24-hour retention window can return the same response; client lists and domain events contain no secret. Client-credentials clients remain limited to `catalogue:read` and `integration:read`.

Administrators can also register and revoke reviewed public OAuth applications through `/api/v1/admin/oauth-clients`. These authorization-code clients use exact HTTPS redirect URIs, user consent and S256 PKCE, and receive no client secret. The operations require an active administrator and delegated `oauth-clients:manage` scope.

Public product endpoints are `GET /api/v1/status` for a point-in-time application response and `GET /api/v1/changelog` for recent API release notes. Status does not probe dependencies or promise availability. Each endpoint is limited to 60 reads per minute per source IP.
