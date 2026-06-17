# Mobile-Money Payout Integration (MTN MoMo + Orange Money) — Design

**Date:** 2026-06-17
**Status:** Approved (design); pending spec review → implementation plan
**Goal:** Pay practitioners their compensation for paid testing programmes via Cameroon mobile money (MTN MoMo and Orange Money), replacing the current manual/offline payout recording with real, admin-triggered disbursements — while keeping manual settlement as a fallback.

---

## Context

The platform already has an offline payout-tracking seam (committed `3991e7f`):
- `App\Services\Payouts\PayoutGateway` interface (`disburse()`, `status()`) + `PayoutResult` DTO.
- `ManualPayoutGateway` (default, live) records payouts an admin settles by hand.
- `MtnMomoPayoutGateway` / `OrangeMoneyPayoutGateway` — stubs that throw until implemented.
- Driver chosen via `config/payouts.php` (`PAYOUT_DRIVER`), bound in `AppServiceProvider`.
- `practitioner_applications` carries `payout_status` (`not_applicable|pending|paid`), `payout_amount`, `payout_currency`, `payout_reference`, `paid_at`. Approving a paid-program application sets `payout_status='pending'` via `PractitionerApplication::markApproved()`.
- The Filament `PractitionerApplicationResource` "Record Payout" action routes through the resolved gateway.

This design extends that seam into real disbursement.

**Customers/payees:** practitioners in Cameroon/CEMAC. Mobile money is the realistic payout rail; Stripe does not pay out to Cameroon.

---

## Approved Decisions

1. **Payee number + network:** add `payout_number` to the practitioner profile (practitioner enters it once). Network (MTN vs Orange) is **auto-detected from the Cameroon number prefix**, with an **admin override** at payout time. No stored network field — derived + overridable.
2. **Trigger + confirmation:** **admin-triggered** "Pay now" → live disbursement → **poll** the provider for final status (no public webhook required).
3. **Orange API risk:** user will provide Orange Money Cameroon API docs/sandbox. **MTN MoMo is implemented now** against its public sandbox spec; **Orange is scaffolded** and its concrete endpoints/auth are filled in from the user's docs (until then it remains an explicit "awaiting Orange spec" stub that throws — never fakes success).

---

## Architecture

Evolve the single-driver binding into a **`PayoutGatewayManager`** that resolves the correct driver **per payout** based on the resolved network:
- `mtn` → `MtnMomoPayoutGateway`
- `orange` → `OrangeMoneyPayoutGateway`
- otherwise (no number / `PAYOUT_DRIVER=manual` / unconfigured) → `ManualPayoutGateway`

The existing `PayoutGateway` interface is unchanged in spirit; the manager (`App\Services\Payouts\PayoutGatewayManager`) becomes the entry point the Filament action and poll command call: `->driverFor(string $network): PayoutGateway` plus `->resolveNetwork(PractitionerApplication, ?string $override): string`.

Keep each driver focused and independently testable; the manager owns selection only.

---

## Data Model Changes

**Migration A — `practitioner_profiles`:**
- `payout_number` (string, nullable) — the practitioner's mobile-money MSISDN (stored normalised, e.g. `2376XXXXXXXX`).

**Migration B — `practitioner_applications`:**
- `payout_provider` (string 20, nullable) — `mtn|orange|manual`, set when a payout is initiated.
- `payout_initiated_at` (timestamp, nullable).
- `payout_failure_reason` (string/text, nullable).
- `payout_status` semantics extended to: `not_applicable | pending | paid | failed`. (Column already exists; no type change needed — values are app-level.)

**Network detection — `App\Services\Payouts\MobileMoneyNetwork`:**
- `detect(string $number): ?string` → `mtn` | `orange` | `null`.
- Cameroon prefixes (normalised to local 9-digit `6XXXXXXXX`, country code stripped):
  - MTN: `67`, `650`–`654`, `680`–`689`.
  - Orange: `69`, `655`–`659`, `640`–`649`.
  - Unknown → `null` (admin must pick).
- `normalise(string $number): string` — strip spaces/`+`, handle `237` country code.

---

## Data Flow

1. Practitioner saves `payout_number` in their profile (profile form + validation).
2. Admin approves a paid-program application → `payout_status='pending'` (existing behaviour).
3. Admin clicks **"Pay now"** on the application:
   - Manager resolves network from `payout_number` prefix (admin override allowed in the form).
   - Driver `disburse()` called with an **idempotent reference = application id** (so retries don't double-pay).
   - On accept: store `payout_provider`, `payout_reference`, `payout_amount`, `payout_currency`, `payout_initiated_at`; `payout_status` stays `pending`.
   - On immediate failure: `payout_status='failed'`, store `payout_failure_reason`; admin notified.
4. Scheduled command **`payouts:poll`** (e.g. every 2–5 min via the scheduler) iterates `pending` applications with a `payout_reference`, calls the driver's `status()`:
   - success → `payout_status='paid'`, `paid_at=now()`; notify practitioner (`CourseCertificateIssued`-style mail / `AdminNotifier`) + admin.
   - failed → `payout_status='failed'`, store reason; notify admin.
   - still pending → leave for next run.

---

## Drivers

### MtnMomoPayoutGateway (implemented now)
MoMo Disbursements sandbox (`https://sandbox.momodeveloper.mtn.com`):
- Auth: `Ocp-Apim-Subscription-Key` + API user/key → `POST /disbursement/token/` for a bearer token (cached until expiry).
- Disburse: `POST /disbursement/v1_0/transfer` with headers `X-Reference-Id` (idempotent UUID derived deterministically from application id), `X-Target-Environment` (`sandbox`/`production`), bearer token; body `{ amount, currency, externalId, payee: { partyIdType: 'MSISDN', partyId }, payerMessage, payeeNote }`. A `202 Accepted` means *queued*, not settled.
- Status: `GET /disbursement/v1_0/transfer/{X-Reference-Id}` → `{ status: PENDING|SUCCESSFUL|FAILED }` → map to pending/paid/failed.
- Config: `config('payouts.mtn_momo.*')`.

### OrangeMoneyPayoutGateway (scaffold now, implement from provided docs)
Same interface and lifecycle (OAuth token → initiate transfer → poll status). Concrete endpoints, auth scheme, and payload shapes filled in from the Orange Money Cameroon docs the user provides. Until then `disburse()`/`status()` throw a clear "Orange driver awaiting API spec" `RuntimeException`.

---

## Safety (money movement)

- **Admin-triggered only** — no auto-disbursement.
- **Idempotency** — reference derived from application id; a guard rejects paying an application already `paid`.
- **No live credential handling by the assistant** — all keys live in `.env` (`MTN_MOMO_*`, `ORANGE_MONEY_*`), supplied by the user. The repo ships sandbox-shaped config only.
- **Verification boundary** — implementation is tested against **mocked HTTP** (`Http::fake`). The assistant cannot verify against the live sandbox; the **user** performs the sandbox-credential end-to-end check before flipping `PAYOUT_DRIVER` off `manual` in production.
- **Manual fallback** — `ManualPayoutGateway` remains for offline settlement / unconfigured networks.

---

## Error Handling

- `disburse()` network/HTTP failure → caught; `payout_status` unchanged-or-`failed` with `payout_failure_reason`; admin notified; retry allowed (same idempotent reference).
- Unconfigured/credential-less driver → throws a clear message surfaced as an admin notification (no silent success).
- Poll command is defensive: per-application try/catch so one bad record doesn't abort the batch; logs failures.

---

## Testing (no live calls)

- **Network detection** — unit tests: representative MTN/Orange prefixes → correct network; unknown → null; normalisation of `+237`/spaces.
- **Manager** — resolves MoMo for mtn, Orange for orange, manual otherwise; honours admin override.
- **MoMo driver** — `Http::fake()` simulating token, `202` transfer accept, and status `SUCCESSFUL`/`FAILED`/`PENDING`; assert state transitions + idempotent reference + failure-reason capture.
- **Idempotency/double-pay guard** — paying a `paid` application is rejected.
- **Poll command** — pending → paid on success; pending → failed on failure; notifications dispatched (`Mail::fake`, DB notification assertions).
- **Filament "Pay now" action** — initiates disbursement and persists provider/reference (driver mocked).
- Orange driver: a test asserting it throws the "awaiting spec" exception until implemented.

---

## Out of Scope (this spec)

- Real Orange Money endpoint implementation (gated on user-provided docs — a follow-up once docs arrive).
- Live/production credential provisioning and the live sandbox verification (user-performed).
- Collecting payments *from* customers (this is payouts *to* practitioners only).
- Webhook-based confirmation (explicitly deferred in favour of polling).

---

## File Map (anticipated)

- `database/migrations/*_add_payout_number_to_practitioner_profiles.php`
- `database/migrations/*_add_disbursement_fields_to_practitioner_applications.php`
- `app/Services/Payouts/MobileMoneyNetwork.php`
- `app/Services/Payouts/PayoutGatewayManager.php`
- `app/Services/Payouts/MtnMomoPayoutGateway.php` (implement)
- `app/Services/Payouts/OrangeMoneyPayoutGateway.php` (scaffold)
- `app/Console/Commands/PollPayouts.php` (+ schedule registration)
- `app/Models/PractitionerProfile.php`, `PractitionerApplication.php` (fillable/casts/helpers)
- `app/Filament/Resources/PractitionerApplicationResource.php` ("Pay now" action + override)
- practitioner profile view + validation for `payout_number`
- `config/payouts.php` (already present; confirm keys)
- Tests under `tests/Feature` / `tests/Unit` per the Testing section.
