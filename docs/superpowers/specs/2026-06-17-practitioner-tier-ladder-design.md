# Practitioner Tier Ladder — Design

**Date:** 2026-06-17
**Status:** Approved (pending spec review)
**Scope:** RBAC verification-gating for practitioner programs, reframed as a tiered standing system.

## 1. Problem & Intent

Paid practitioner programs should be a privilege of standing, not open to anyone with an
account — but the mechanism must confer **distinction, class, and levels**, not exclusion.
Everyone can participate from day one; standing is earned and visible. Verification unlocks
paid work; sustained quality work earns prestige and priority.

## 2. The Ladder

A four-rung ladder. Higher rungs add capability and prestige; nobody is locked out of
starting.

| Tier | How reached | Unlocks |
|------|-------------|---------|
| **Associate** | Default for every practitioner | Volunteer programs |
| **Verified** | Admin grants `is_verified` (identity / registration check) | + Paid programs |
| **Distinguished** | Verified **and** ≥ 3 published findings | + Prestige badge, priority on paid programs |
| **Fellow** | Verified **and** ≥ 8 published findings | + Top badge, highest priority |

### Promotion model — hybrid

- **Associate** is the default for everyone (no profile needed).
- **Verified** is granted **explicitly by an admin** via the existing
  `practitioner_profiles.is_verified` toggle. Identity/registration verification is a human
  judgment.
- **Distinguished** and **Fellow** are earned **automatically** from track record, but only
  once verified.

### Retroactive merit (intentional)

An unverified practitioner may do volunteer work and accumulate published findings. Those
findings count the moment an admin verifies them — so verification can promote someone
straight past Verified to Distinguished or Fellow. Prior volunteer contribution is rewarded
retroactively.

## 3. Data Model — computed, not stored

No new columns. Tier is **derived on demand**.

### `App\Enums\PractitionerTier` (PHP enum)

Cases: `Associate`, `Verified`, `Distinguished`, `Fellow`.

Behavior:
- `label(): string` — display name.
- `badge(): array|string` — color/icon for blade + Filament badges.
- `level(): int` — 0–3, for comparison and priority sorting.
- `canApplyToPaid(): bool` — `true` for Verified and above.

Thresholds live as constants on the enum so they are tunable in one place:
- `DISTINGUISHED_FINDINGS = 3`
- `FELLOW_FINDINGS = 8`

### `User::practitionerTier(): PractitionerTier`

```
no profile OR not is_verified          → Associate
is_verified, < 3 published findings     → Verified
is_verified, 3–7 published findings     → Distinguished
is_verified, ≥ 8 published findings      → Fellow
```

"Published findings" = `PractitionerFinding` where `practitioner_id` = user and
`is_published = true`.

Single source of truth, no denormalized column to drift. If priority sorting on large
applicant lists ever becomes slow, a cached column can be added later — additive change, not
now.

## 4. The Gate — `ProgramController@apply`

After the existing open / full / duplicate checks, add:

```php
if ($program->type === 'paid') {
    abort_unless(
        auth()->user()->practitionerTier()->canApplyToPaid(),
        403,
        'Paid programmes are open to verified practitioners. Complete your profile and request verification to apply.'
    );
}
```

Volunteer programs are untouched — open to all authenticated practitioners. The existing
`isOpen()`, `isFull()`, and duplicate-application guards are unchanged.

## 5. UI / Display

- **Programs index & show** (`resources/views/practitioner/programs/*.blade.php`) — paid
  programs get a "Verified required" chip. When the current user cannot apply to a paid
  program, the Apply button renders **disabled** with the explanatory message, instead of
  letting them hit a 403.
- **Practitioner dashboard / profile** (`dashboard.blade.php`, `profile.blade.php`) — show
  the practitioner's current tier badge with a one-line "how to reach the next rung."
- **Filament `PractitionerProfileResource`** — read-only computed "Tier" badge
  column/field alongside the existing `is_verified` toggle.
- **Filament `PractitionerApplicationResource`** — show applicant tier as a badge; on a paid
  program's applicants list, default-sort by tier `level` descending, then `created_at`
  ascending. This is the "priority" mechanic — higher tiers surface first.

## 6. Testing

- Associate (no profile) → can apply to a volunteer program; **blocked (403)** from a paid
  program.
- Verified → can apply to a paid program.
- Tier computation at each boundary: 0 and 2 findings → Verified; 3 and 7 → Distinguished;
  8 → Fellow.
- Unverified practitioner with 10 published findings → still Associate.
- Verifying a practitioner who already has 8 published findings → tier is Fellow.
- Paid applicants list sorts Fellow → Distinguished → Verified → (Associate cannot appear,
  as they cannot apply to paid).

## 7. Out of Scope (explicit)

- Payout logic (separate Phase 1 spec).
- Media uploads + video embed (separate spec).
- A `min_tier` field per program for truly exclusive programs (additive future change).
- Notifications / emails on promotion.
- Demotion handling beyond what falls out naturally from the computed tier (e.g.
  un-verifying drops a practitioner back to Associate automatically).
