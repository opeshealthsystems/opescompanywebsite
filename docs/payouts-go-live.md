# Mobile-Money Payouts — Go-Live Runbook

How to take practitioner payouts from the default **manual** mode to live MTN MoMo
(and later Orange Money). The code is built and tested against mocked HTTP; these
are the human-owned steps that finish the loop.

> Security: never paste API keys into chat, tickets, or commits. Keys live only
> in `.env` (which is git-ignored). If a key is ever exposed, rotate it in the
> provider portal immediately.

---

## Current behaviour (out of the box)

- `PAYOUT_DRIVER=manual` (default). Admins record payouts offline via the
  practitioner application's **Pay now** action — no money moves automatically.
- Practitioners set their mobile-money number under **Practitioner portal →
  Profile → Payout Details**. The network (MTN/Orange) is auto-detected from the
  number prefix; an admin can override it at payout time.

---

## Enabling MTN MoMo (sandbox first)

1. **Create a MoMo developer account** at https://momodeveloper.mtn.com and
   subscribe to the **Disbursements** product to get a **Subscription Key**.
2. **Provision an API User + API Key** in the sandbox (MoMo's provisioning flow
   uses your subscription key to create an `apiUser` UUID and an `apiKey`).
3. Put the values in `.env` (NOT committed):
   ```
   PAYOUT_DRIVER=mtn_momo
   PAYOUT_CURRENCY=XAF
   MTN_MOMO_BASE_URL=https://sandbox.momodeveloper.mtn.com
   MTN_MOMO_SUBSCRIPTION_KEY=<your subscription key>
   MTN_MOMO_API_USER=<provisioned api user uuid>
   MTN_MOMO_API_KEY=<provisioned api key>
   MTN_MOMO_ENVIRONMENT=sandbox
   ```
4. `php artisan config:clear`.
5. **End-to-end sandbox test:**
   - Give a test practitioner a sandbox MSISDN as their payout number.
   - Approve a *paid* program application for them → status becomes `pending`.
   - Click **Pay now** in the admin panel → the app calls MoMo `/transfer`
     (a `202` means queued) and stores the reference + `payout_initiated_at`.
   - Run the confirmation poll: `php artisan payouts:poll` (or wait for the
     scheduler, which runs it every 5 minutes). On `SUCCESSFUL` the application
     flips to `paid`, `paid_at` is stamped, and the practitioner is emailed
     (`PayoutSettled`). On `FAILED` it flips to `failed` with a reason and staff
     are notified.
6. **Production:** when sandbox is verified, switch `MTN_MOMO_BASE_URL` and
   `MTN_MOMO_ENVIRONMENT` to production values and use production credentials.

### Scheduler
`payouts:poll` is already registered in `routes/console.php`
(`->everyFiveMinutes()->withoutOverlapping()`). Ensure the Laravel scheduler is
running in production: `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`.

---

## Enabling Orange Money (pending API spec)

`OrangeMoneyPayoutGateway` is a scaffold that intentionally throws until
implemented. To complete it:
1. Obtain Orange Money Cameroon API docs/sandbox (endpoints, OAuth, payloads).
2. Implement `disburse()` / `status()` in
   `app/Services/Payouts/OrangeMoneyPayoutGateway.php`, mirroring the MoMo driver
   (token → initiate transfer → poll status, mapped to `paid`/`failed`/`pending`).
3. Fill `ORANGE_MONEY_*` in `.env`.
No other code changes are needed — `PayoutGatewayManager`, the **Pay now** action,
and `payouts:poll` already route `orange`-network payouts to this driver.

---

## Verify the number prefixes

`app/Services/Payouts/MobileMoneyNetwork.php` maps Cameroon prefixes to networks
(MTN: `67`, `650`–`654`, `680`–`689`; Orange: `69`, `655`–`659`, `640`–`649`).
Confirm these against the current ARTEC/operator numbering plan and adjust if
needed. Unknown prefixes return `null`, forcing the admin to pick the network at
payout time — so a wrong/missing prefix never silently mis-routes a payout.

---

## Safety properties (built in)

- **Admin-triggered only** — no automatic disbursement.
- **Idempotent** — the payout reference is reused on retry; `isPayable()` blocks
  paying an application that is already `paid`.
- **Best-effort notifications** — a notification failure cannot break a settlement.
- **Manual fallback** — set `PAYOUT_DRIVER=manual` (or leave a practitioner's
  number blank) to record payouts offline.
