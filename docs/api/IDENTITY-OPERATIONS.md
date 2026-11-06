# API identity and client onboarding

OHS uses Laravel Passport 13.10 and League OAuth2 Server for approved server-to-server clients, administrator-reviewed public OAuth clients, and developer-owned public OAuth applications. `POST /api/v1/oauth/token` remains the machine-only client-credentials endpoint. Delegated user tokens use authorization code with S256 PKCE and `POST /api/v1/oauth/user-token`. This is OAuth 2.0 authorization, not OpenID Connect authentication. Password/implicit grants, userinfo, organization tenancy and OIDC are not published. Existing web sessions and password resets remain the website sign-in mechanism.

Public developer entry points are `/en/developers` and `/fr/developers`; signed-in application management is available at `/en/developers/console` and `/fr/developers/console`. Public OAuth and machine API client web forms dispatch the same owner-scoped application workflows as the versioned API; lifecycle writes use 24-hour idempotent command receipts. The console displays daily and 30-day usage scoped to the signed-in user/application or machine client. The versioned contract is `/api/v1/openapi.json`. OpenAPI `0.115.0` documents delegated authorization, business workflows, administrator client lifecycle operations, and signed-in user self-service for public OAuth and restricted machine clients. `GET /api/v1/api-products` lists current API families, access methods, scopes and data boundaries. A segregated sandbox, organization tenancy, and published support/SLO plans remain unavailable.

## Delegate user authorization

Public OAuth applications can be registered by a signed-in user with a delegated token. The API only grants currently available user scopes, binds ownership to that account, limits accounts to five active apps and issues no client secret. User consent and S256 PKCE remain required. Redirects must be HTTPS, a loopback URL for local development, or the reserved `opeshealth://oauth/callback` URI. Use the app lifecycle endpoints below to manage those applications.

Signed-in users can also manage up to five machine API clients. These are bound to the creating user and restricted to `catalogue:read` and `integration:read`; they cannot access patient records or role-scoped business workflows. The client secret is returned only at creation or rotation, so store it in a server-side secret manager immediately. The SDK supports these lifecycle operations through an `OpesBusinessApiClient` configured with the user's delegated access token. Machine requests themselves use the `client_credentials` grant and the selected machine scopes. Create, rotate, and revoke requests require an idempotency key; rotation and revocation immediately invalidate existing access tokens.

Administrators can also create a reviewed public client through the command when a supported workflow requires operator review:

```sh
php artisan api:user-client --owner="admin@opeshealthsystems.com" --name="Approved browser integration" --redirect="https://consumer.example/callback" --scope="identity:read"
```

The authorization request must include `response_type=code`, the client ID, an exact registered `redirect_uri`, a unique `state` of at least 32 characters, `code_challenge_method=S256`, and a valid S256 `code_challenge`. The signed-in user sees a branded consent screen and may allow or deny. Authorization codes are single-use and bound to the client, callback, user and PKCE verifier. Exchange them at `/api/v1/oauth/user-token`; authorization-code and refresh-token grants are supported there only. Access tokens expire after 15 minutes and refresh tokens after seven days. Registered scopes are the reviewed app ceiling; each user's active roles are checked independently on issuance and each API request.

Current user status, role-derived approved scopes, client owner status and token/client/user association are checked again on token refresh and every private API request. A session cookie alone is not API authorization, and a machine token cannot call user business operations. Do not put access or refresh tokens in URLs or logs. Revoke the client with `php artisan api:client revoke --owner="admin@opeshealthsystems.com" --client=CLIENT_UUID`; this revokes its existing tokens. The machine-client `rotate` action deliberately rejects public PKCE clients because they have no client secret to rotate.

Business operations use the context paths listed in the OpenAPI contract, such as `/api/v1/customer/profile` and `/api/v1/administration/projects`, with role-derived OAuth scopes. Their results use explicit resource field allowlists; command requests require an `Idempotency-Key`. `GET /api/v1/me` returns only the authenticated user's own name, email, role names and currently effective scopes for role-aware client navigation. `GET /api/v1/me/activity` returns only the authenticated user's activity projection. OpenAPI role/scope requirements supplement the application's existing policy and record-level checks; they do not replace them.

## Provision a reviewed client

Run on the server from the application root. Specify an active administrator who is accountable for this integration. This association does not make the machine token a user login or grant access to that administrator's records.

```sh
php artisan api:client create --name="Approved integration name" --owner="admin@opeshealthsystems.com" --scope=catalogue:read --scope=integration:read
```

The command prints a client ID and private credential-file path. It never prints the secret. The JSON is written with mode 0600 under `storage/app/private/api-credentials/`; deliver it to the approved consumer through an appropriate private channel, then remove that credential file after verified handoff. Do not commit it, attach it to public support tickets, put it in browser storage, or include it in access logs. Secret hashes alone are stored in `oauth_clients`.

The owner must remain active. Provisioning permits only the explicit scopes `catalogue:read` and `integration:read`; no wildcard grants. Clients with no approved owner or scope list cannot obtain tokens. Ownership here is administrative accountability, not a completed multi-organization tenancy model.

## Use and limits

Exchange credentials as a form-encoded POST body (or supported confidential-client HTTP Basic authentication) over HTTPS. Request only approved scopes. Tokens expire in 900 seconds, with no refresh or ID token. Request a new token when needed rather than minting one per catalogue call. Keep secrets and bearer tokens on the consumer's server.

- `catalogue:read`: bounded, explicitly whitelisted published product metadata at `GET /api/v1/catalogue/products`. Page size 1–50. English/French supported.
- `integration:read`: the current token client's identity, granted scopes and UTC daily authenticated-attempt count at `GET /api/v1/integration`. `GET /api/v1/integration/usage` returns the same client's 30-day UTC series (`period_start`, `period_end`, `requests_today`, `requests_last_30_days`, and 30 `daily_usage` buckets). A caller cannot select another client by ID. Active administrators can use the separate delegated `api-clients:read` support report to inspect one managed machine client.
- Token issuance: 20 attempts/minute per source IP. Protected resources: 60 authorized attempts/minute per client. The daily counter includes current calls, validation failures and quota rejections after successful identity/scope checks; it excludes unauthenticated/scope-denied calls and token issuance.

The token endpoint uses standard OAuth error JSON. Protected API endpoints use Problem Details for 401/403/422/429 errors, with request correlation identifiers. Resource authentication validates the signed token through League, its stored token/client association, database expiry/revocation, active owner and current scope approval on each request. A website session alone cannot access these machine endpoints.

## Rotate or revoke

```sh
php artisan api:client list --owner="admin@opeshealthsystems.com"
php artisan api:client rotate --owner="admin@opeshealthsystems.com" --client=CLIENT_UUID
php artisan api:client revoke --owner="admin@opeshealthsystems.com" --client=CLIENT_UUID
```

Rotation creates a new private credential file and immediately revokes existing access tokens. The client ID stays the same; old secrets stop working. Coordinate that brief consumer interruption. Revocation disables minting and existing access immediately. Revoked clients cannot be rotated back into service. Deactivating the accountable user also blocks client access.

## Keys and deployment

Signing keys live under `storage/app/private/oauth/`, outside public assets. Generate them once with `php artisan passport:keys`; never use `--force` during normal releases. Keep private keys mode 0600 and back them up securely with restricted access. A signing-key replacement invalidates existing tokens and needs a separately planned rotation. No secrets or private keys belong in the source release archive.

The additive migrations create the standard Passport tables and explicit client scopes/usage storage. Preserve these tables on source rollback. Build dependencies in the staged release before maintenance, back up source/database, then switch the prepared vendor directory alongside the reviewed application files. Restore both previous source and vendor on a failed cutover. Website enquiry outbox/idempotency tables are unaffected.

## Remaining identity gates

OIDC, MFA for a future developer control panel, organization-owned application registration, machine-client self-service, and additional private API workflows remain separate work. Do not expose private resources based solely on OAuth scopes or label this release as federated SSO. The business contract is a first product surface, but it does not mean every internal/admin action or the entire application has been converted to an API.

References: [Laravel Passport](https://laravel.com/framework/docs/13.x/passport), [OAuth security best current practice](https://www.rfc-editor.org/rfc/rfc9700.html).




## Manage machine clients through the API

Active administrators can inspect machine-client records through the existing `GET /api/v1/admin/api-clients` operation with `api-clients:manage`. The new `GET /api/v1/admin/api-clients/{client}/usage` support report uses the narrower delegated `api-clients:read` scope. It contains 30 UTC daily buckets for that client and exposes no credential material. Create, rotate, and revoke use `POST /api/v1/admin/api-clients`, `POST /api/v1/admin/api-clients/{client}/rotate`, and `DELETE /api/v1/admin/api-clients/{client}` with `api-clients:manage`. Mutations require an `Idempotency-Key`. Create and rotate return the plaintext secret in the protected `Cache-Control: no-store` response; replaying the same idempotency key and payload within 24 hours can return the same response. Store it securely. Lists, usage reports, events, and logs do not include the secret. These APIs enforce the same active administrator ownership and allowed machine scopes as CLI provisioning.


## Manage reviewed public OAuth clients through the API

An active administrator can list, register, and revoke public authorization-code clients through `GET /api/v1/admin/oauth-clients`, `POST /api/v1/admin/oauth-clients`, and `DELETE /api/v1/admin/oauth-clients/{client}`. Use a delegated user token carrying `oauth-clients:manage`; mutations require an `Idempotency-Key`. New clients are administrator-owned, non-confidential OAuth clients using user consent and S256 PKCE. Register exact HTTPS redirect URIs and only scopes allowed for the owner. No client secret is issued. Revocation disables the client and its existing tokens.

## Manage your public OAuth applications

Any active signed-in account with `identity:read` and/or `identity:write` can manage its own public authorization-code apps. `GET /api/v1/developer/applications` lists only that account's apps. `POST /api/v1/developer/applications` registers an app, `PATCH /api/v1/developer/applications/{client}` replaces its name, callbacks and scopes, and `DELETE` revokes the app and all its tokens. Every mutation requires an `Idempotency-Key`. Registration and update accept only `name`, `redirect_uris` and `scopes`; unknown fields are rejected. The account can have at most five active public apps. Callback rules and scope availability are checked on every create/update. Client secrets are never issued. Updating/revoking immediately invalidates tokens previously issued to the app. Use the same account token for the list and lifecycle endpoints; a different owner's application returns 404.

Example registration body:

```json
{
  "name": "My OPES Health integration",
  "redirect_uris": ["https://app.example.com/oauth/callback"],
  "scopes": ["identity:read"]
}
```
