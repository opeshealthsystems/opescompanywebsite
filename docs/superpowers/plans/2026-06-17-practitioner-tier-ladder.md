# Practitioner Tier Ladder Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Introduce a four-rung practitioner standing ladder (Associate → Verified → Distinguished → Fellow) that gates paid programs behind verification and confers prestige + admin priority on the merit tiers.

**Architecture:** Tier is a computed value, not a stored column. A `PractitionerTier` PHP enum holds all tier behavior (labels, badge styling, level, thresholds, paid-access rule). `User::practitionerTier()` derives the tier from `practitioner_profiles.is_verified` plus the count of published findings. The web gate lives in `ProgramController@apply`; blade views and Filament resources read the tier for display, and a query scope reproduces tier ordering for admin priority sort.

**Tech Stack:** Laravel 13, PHP 8.2 enums, Filament v3, PHPUnit 12, Tailwind blade views.

**Spec:** `docs/superpowers/specs/2026-06-17-practitioner-tier-ladder-design.md`

**Test command:** `php artisan test` (single-class: `php artisan test --filter=ClassName`).

---

## File Structure

- **Create** `app/Enums/PractitionerTier.php` — the enum: cases, threshold constants, `forProfile()`, `label()`, `description()`, `level()`, `canApplyToPaid()`, `tailwindBadge()`, `filamentColor()`, `next()`.
- **Modify** `app/Models/User.php` — add `practitionerFindings()` relation + `practitionerTier()` method.
- **Modify** `app/Models/PractitionerApplication.php` — add `scopeByTierPriority()` query scope.
- **Modify** `app/Http/Controllers/Practitioner/ProgramController.php` — add the paid-program verification gate in `apply()`.
- **Modify** `resources/views/practitioner/programs/index.blade.php` — "Verified required" chip + disabled apply button for ineligible users.
- **Modify** `resources/views/practitioner/programs/show.blade.php` — gated apply panel.
- **Modify** `resources/views/practitioner/dashboard.blade.php` — current tier badge + next-rung hint.
- **Modify** `app/Filament/Resources/PractitionerProfileResource.php` — read-only Tier badge column.
- **Modify** `app/Filament/Resources/PractitionerApplicationResource.php` — Tier badge column + default tier-priority sort.
- **Create** `tests/Unit/PractitionerTierTest.php` — enum + accessor unit tests.
- **Modify** `tests/Feature/PractitionerPortalTest.php` — gate + view feature tests.

---

## Task 1: `PractitionerTier` enum

**Files:**
- Create: `app/Enums/PractitionerTier.php`
- Test: `tests/Unit/PractitionerTierTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Unit/PractitionerTierTest.php`:

```php
<?php

namespace Tests\Unit;

use App\Enums\PractitionerTier;
use PHPUnit\Framework\TestCase;

class PractitionerTierTest extends TestCase
{
    public function test_unverified_is_always_associate_regardless_of_findings(): void
    {
        $this->assertSame(PractitionerTier::Associate, PractitionerTier::forProfile(false, 0));
        $this->assertSame(PractitionerTier::Associate, PractitionerTier::forProfile(false, 10));
    }

    public function test_verified_findings_thresholds(): void
    {
        $this->assertSame(PractitionerTier::Verified, PractitionerTier::forProfile(true, 0));
        $this->assertSame(PractitionerTier::Verified, PractitionerTier::forProfile(true, 2));
        $this->assertSame(PractitionerTier::Distinguished, PractitionerTier::forProfile(true, 3));
        $this->assertSame(PractitionerTier::Distinguished, PractitionerTier::forProfile(true, 7));
        $this->assertSame(PractitionerTier::Fellow, PractitionerTier::forProfile(true, 8));
        $this->assertSame(PractitionerTier::Fellow, PractitionerTier::forProfile(true, 50));
    }

    public function test_only_verified_and_above_can_apply_to_paid(): void
    {
        $this->assertFalse(PractitionerTier::Associate->canApplyToPaid());
        $this->assertTrue(PractitionerTier::Verified->canApplyToPaid());
        $this->assertTrue(PractitionerTier::Distinguished->canApplyToPaid());
        $this->assertTrue(PractitionerTier::Fellow->canApplyToPaid());
    }

    public function test_levels_are_strictly_increasing(): void
    {
        $this->assertSame(0, PractitionerTier::Associate->level());
        $this->assertSame(1, PractitionerTier::Verified->level());
        $this->assertSame(2, PractitionerTier::Distinguished->level());
        $this->assertSame(3, PractitionerTier::Fellow->level());
    }

    public function test_next_returns_following_tier_or_null_at_top(): void
    {
        $this->assertSame(PractitionerTier::Verified, PractitionerTier::Associate->next());
        $this->assertSame(PractitionerTier::Fellow, PractitionerTier::Distinguished->next());
        $this->assertNull(PractitionerTier::Fellow->next());
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=PractitionerTierTest`
Expected: FAIL — `Class "App\Enums\PractitionerTier" not found`.

- [ ] **Step 3: Write minimal implementation**

Create `app/Enums/PractitionerTier.php`:

```php
<?php

namespace App\Enums;

enum PractitionerTier: string
{
    case Associate = 'associate';
    case Verified = 'verified';
    case Distinguished = 'distinguished';
    case Fellow = 'fellow';

    /** Published-findings thresholds (only apply once verified). */
    public const DISTINGUISHED_FINDINGS = 3;
    public const FELLOW_FINDINGS = 8;

    /** Derive a tier from verification status + published-findings count. */
    public static function forProfile(bool $isVerified, int $publishedFindings): self
    {
        if (! $isVerified) {
            return self::Associate;
        }

        if ($publishedFindings >= self::FELLOW_FINDINGS) {
            return self::Fellow;
        }

        if ($publishedFindings >= self::DISTINGUISHED_FINDINGS) {
            return self::Distinguished;
        }

        return self::Verified;
    }

    public function label(): string
    {
        return match ($this) {
            self::Associate => 'Associate',
            self::Verified => 'Verified',
            self::Distinguished => 'Distinguished',
            self::Fellow => 'Fellow',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Associate => 'Open to volunteer programmes. Get verified to unlock paid programmes.',
            self::Verified => 'Verified practitioner — eligible for paid programmes.',
            self::Distinguished => 'Distinguished — recognised for sustained, published contributions.',
            self::Fellow => 'Fellow — the highest standing, with top priority on paid programmes.',
        };
    }

    public function level(): int
    {
        return match ($this) {
            self::Associate => 0,
            self::Verified => 1,
            self::Distinguished => 2,
            self::Fellow => 3,
        };
    }

    public function canApplyToPaid(): bool
    {
        return $this->level() >= self::Verified->level();
    }

    /** Tailwind chip classes for blade views. */
    public function tailwindBadge(): string
    {
        return match ($this) {
            self::Associate => 'bg-slate-700 text-slate-300',
            self::Verified => 'bg-emerald-900 text-emerald-300',
            self::Distinguished => 'bg-sky-900 text-sky-300',
            self::Fellow => 'bg-amber-900 text-amber-300',
        };
    }

    /** Filament badge color name. */
    public function filamentColor(): string
    {
        return match ($this) {
            self::Associate => 'gray',
            self::Verified => 'success',
            self::Distinguished => 'info',
            self::Fellow => 'warning',
        };
    }

    /** The next rung up, or null at the top. */
    public function next(): ?self
    {
        return match ($this) {
            self::Associate => self::Verified,
            self::Verified => self::Distinguished,
            self::Distinguished => self::Fellow,
            self::Fellow => null,
        };
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=PractitionerTierTest`
Expected: PASS (5 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Enums/PractitionerTier.php tests/Unit/PractitionerTierTest.php
git commit -m "feat(practitioner): add PractitionerTier enum"
```

---

## Task 2: `User::practitionerTier()` accessor

**Files:**
- Modify: `app/Models/User.php`
- Test: `tests/Feature/PractitionerPortalTest.php`

- [ ] **Step 1: Write the failing test**

Add these imports near the top of `tests/Feature/PractitionerPortalTest.php` (after the existing `use` block):

```php
use App\Enums\PractitionerTier;
use App\Models\PractitionerFinding;
```

Add these test methods inside the `PractitionerPortalTest` class (before the final closing brace):

```php
// ── Tier ladder ───────────────────────────────────────────────────────

public function test_unverified_practitioner_is_associate_even_with_published_findings(): void
{
    $practitioner = $this->practitioner(); // profile is_verified defaults to false

    PractitionerFinding::factory()->published()->count(10)->create([
        'practitioner_id' => $practitioner->id,
    ]);

    $this->assertSame(PractitionerTier::Associate, $practitioner->fresh()->practitionerTier());
}

public function test_verified_practitioner_tier_climbs_with_published_findings(): void
{
    $practitioner = $this->practitioner();
    $practitioner->practitionerProfile->update(['is_verified' => true]);

    $this->assertSame(PractitionerTier::Verified, $practitioner->fresh()->practitionerTier());

    PractitionerFinding::factory()->published()->count(3)->create([
        'practitioner_id' => $practitioner->id,
    ]);
    $this->assertSame(PractitionerTier::Distinguished, $practitioner->fresh()->practitionerTier());

    PractitionerFinding::factory()->published()->count(5)->create([
        'practitioner_id' => $practitioner->id,
    ]);
    $this->assertSame(PractitionerTier::Fellow, $practitioner->fresh()->practitionerTier());
}

public function test_unpublished_findings_do_not_count_toward_tier(): void
{
    $practitioner = $this->practitioner();
    $practitioner->practitionerProfile->update(['is_verified' => true]);

    PractitionerFinding::factory()->count(8)->create([
        'practitioner_id' => $practitioner->id,
        'is_published'    => false,
    ]);

    $this->assertSame(PractitionerTier::Verified, $practitioner->fresh()->practitionerTier());
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=PractitionerPortalTest`
Expected: FAIL — `Call to undefined method App\Models\User::practitionerTier()`.

- [ ] **Step 3: Write minimal implementation**

In `app/Models/User.php`, add a `use App\Enums\PractitionerTier;` import at the top with the other imports. Then add these two methods inside the `User` class, immediately after the existing `practitionerProfile()` method (around line 114):

```php
public function practitionerFindings(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(\App\Models\PractitionerFinding::class, 'practitioner_id');
}

public function practitionerTier(): PractitionerTier
{
    $isVerified = (bool) $this->practitionerProfile?->is_verified;
    $publishedFindings = $this->practitionerFindings()->where('is_published', true)->count();

    return PractitionerTier::forProfile($isVerified, $publishedFindings);
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=PractitionerPortalTest`
Expected: PASS (existing tests + 3 new tier tests).

- [ ] **Step 5: Commit**

```bash
git add app/Models/User.php tests/Feature/PractitionerPortalTest.php
git commit -m "feat(practitioner): derive practitioner tier from verification and findings"
```

---

## Task 3: Paid-program verification gate

**Files:**
- Modify: `app/Http/Controllers/Practitioner/ProgramController.php:37-45`
- Test: `tests/Feature/PractitionerPortalTest.php`

- [ ] **Step 1: Write the failing test**

Add these test methods inside `PractitionerPortalTest` (after the tier tests from Task 2):

```php
public function test_associate_cannot_apply_to_paid_program(): void
{
    $practitioner = $this->practitioner(); // unverified → Associate
    $program      = PractitionerProgram::factory()->paid()->create();

    $this->actingAs($practitioner)
        ->post('/en/practitioner/programs/' . $program->id . '/apply', [
            'motivation' => 'I would like to join.',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('practitioner_applications', 0);
}

public function test_verified_practitioner_can_apply_to_paid_program(): void
{
    \Illuminate\Support\Facades\Mail::fake();

    $practitioner = $this->practitioner();
    $practitioner->practitionerProfile->update(['is_verified' => true]);
    $program = PractitionerProgram::factory()->paid()->create();

    $this->actingAs($practitioner)
        ->post('/en/practitioner/programs/' . $program->id . '/apply', [
            'motivation' => 'Verified and ready.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('practitioner_applications', [
        'practitioner_id' => $practitioner->id,
        'program_id'      => $program->id,
    ]);
}

public function test_associate_can_still_apply_to_volunteer_program(): void
{
    \Illuminate\Support\Facades\Mail::fake();

    $practitioner = $this->practitioner(); // unverified
    $program      = PractitionerProgram::factory()->create(); // volunteer by default

    $this->actingAs($practitioner)
        ->post('/en/practitioner/programs/' . $program->id . '/apply', [
            'motivation' => 'Happy to volunteer.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('practitioner_applications', [
        'practitioner_id' => $practitioner->id,
        'program_id'      => $program->id,
    ]);
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=PractitionerPortalTest`
Expected: FAIL — `test_associate_cannot_apply_to_paid_program` expects 403 but gets 302 (no gate yet).

- [ ] **Step 3: Write minimal implementation**

In `app/Http/Controllers/Practitioner/ProgramController.php`, in the `apply()` method, add the gate immediately after the existing `abort_if($program->isFull(), ...)` line (line 40), before the duplicate-application check:

```php
        if ($program->type === 'paid') {
            abort_unless(
                auth()->user()->practitionerTier()->canApplyToPaid(),
                403,
                'Paid programmes are open to verified practitioners. Complete your profile and request verification to apply.'
            );
        }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=PractitionerPortalTest`
Expected: PASS (all portal tests including the 3 new gate tests).

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Practitioner/ProgramController.php tests/Feature/PractitionerPortalTest.php
git commit -m "feat(practitioner): gate paid-program applications behind verification"
```

---

## Task 4: Program views — chip + disabled apply for ineligible users

**Files:**
- Modify: `resources/views/practitioner/programs/show.blade.php:85-112`
- Modify: `resources/views/practitioner/programs/index.blade.php:56-64`
- Test: `tests/Feature/PractitionerPortalTest.php`

- [ ] **Step 1: Write the failing test**

Add these test methods inside `PractitionerPortalTest`:

```php
public function test_paid_program_show_blocks_apply_form_for_associate(): void
{
    $practitioner = $this->practitioner(); // unverified
    $program      = PractitionerProgram::factory()->paid()->create([
        'title' => 'Paid Pilot Programme',
    ]);

    $this->actingAs($practitioner)
        ->get('/en/practitioner/programs/' . $program->id)
        ->assertOk()
        ->assertSee('open to verified practitioners')
        ->assertDontSee('Submit Application');
}

public function test_paid_program_show_allows_apply_form_for_verified(): void
{
    $practitioner = $this->practitioner();
    $practitioner->practitionerProfile->update(['is_verified' => true]);
    $program = PractitionerProgram::factory()->paid()->create();

    $this->actingAs($practitioner)
        ->get('/en/practitioner/programs/' . $program->id)
        ->assertOk()
        ->assertSee('Submit Application');
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=PractitionerPortalTest`
Expected: FAIL — `test_paid_program_show_blocks_apply_form_for_associate` sees "Submit Application" (form not yet gated) / does not see the message.

- [ ] **Step 3: Write minimal implementation**

In `resources/views/practitioner/programs/show.blade.php`, replace the `@elseif($program->isOpen() && !$program->isFull())` branch opening (line 85) and wrap its apply form. Change line 85 from:

```blade
    @elseif($program->isOpen() && !$program->isFull())
```

to:

```blade
    @elseif($program->isOpen() && !$program->isFull() && $program->type === 'paid' && !auth()->user()->practitionerTier()->canApplyToPaid())
        <div class="bg-slate-900 border border-amber-800/60 rounded-xl p-6">
            <div class="flex items-start gap-3">
                <i data-lucide="lock" style="width:20px;height:20px;color:#f59e0b;flex-shrink:0;margin-top:2px"></i>
                <div>
                    <h2 class="text-white font-semibold text-base mb-1">Verification required</h2>
                    <p class="text-slate-400 text-sm">This paid programme is open to verified practitioners. Complete your profile and request verification to apply.</p>
                    <a href="{{ route('practitioner.profile', ['locale' => app()->getLocale()]) }}"
                       class="inline-block mt-2 text-sm text-amber-300 underline hover:text-amber-100">Go to Profile →</a>
                </div>
            </div>
        </div>
    @elseif($program->isOpen() && !$program->isFull())
```

This inserts a new gated branch *before* the existing open branch, so verified users (and all volunteer programs) fall through to the unchanged apply form. The phrase "open to verified practitioners" satisfies the test assertion.

In `resources/views/practitioner/programs/index.blade.php`, replace the apply button block (lines 56-64) — the `@else` branch that renders the "Apply Now" form — with a version that disables it for ineligible paid programs. Change:

```blade
                        @else
                            <form method="POST" action="{{ route('practitioner.programs.apply', ['locale' => app()->getLocale(), 'program' => $program->id]) }}">
                                @csrf
                                <button type="submit"
                                    class="text-sm font-semibold px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white transition-colors border-0 cursor-pointer">
                                    Apply Now
                                </button>
                            </form>
                        @endif
```

to:

```blade
                        @elseif($program->type === 'paid' && !auth()->user()->practitionerTier()->canApplyToPaid())
                            <span class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-800 text-amber-400/80 flex items-center gap-1" title="Verification required to apply">
                                <i data-lucide="lock" style="width:12px;height:12px"></i> Verified only
                            </span>
                        @else
                            <form method="POST" action="{{ route('practitioner.programs.apply', ['locale' => app()->getLocale(), 'program' => $program->id]) }}">
                                @csrf
                                <button type="submit"
                                    class="text-sm font-semibold px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white transition-colors border-0 cursor-pointer">
                                    Apply Now
                                </button>
                            </form>
                        @endif
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=PractitionerPortalTest`
Expected: PASS (including the 2 new view tests).

- [ ] **Step 5: Commit**

```bash
git add resources/views/practitioner/programs/show.blade.php resources/views/practitioner/programs/index.blade.php tests/Feature/PractitionerPortalTest.php
git commit -m "feat(practitioner): show verification gate on paid programmes in UI"
```

---

## Task 5: Dashboard tier badge + next-rung hint

**Files:**
- Modify: `resources/views/practitioner/dashboard.blade.php:1-20`
- Test: `tests/Feature/PractitionerPortalTest.php`

- [ ] **Step 1: Write the failing test**

Add this test method inside `PractitionerPortalTest`:

```php
public function test_dashboard_displays_current_tier_badge(): void
{
    $practitioner = $this->practitioner();
    $practitioner->practitionerProfile->update(['is_verified' => true]);

    $this->actingAs($practitioner)
        ->get('/en/practitioner/dashboard')
        ->assertOk()
        ->assertSee('Verified');
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_dashboard_displays_current_tier_badge`
Expected: FAIL — the dashboard does not render the tier label "Verified".

> Note: a plain Associate dashboard already contains no "Verified" text, so this assertion is meaningful. The existing `test_practitioner_can_load_dashboard_and_profile` continues to pass because it only asserts `assertOk()`.

- [ ] **Step 3: Write minimal implementation**

In `resources/views/practitioner/dashboard.blade.php`, inside the header `<div>` that holds the welcome text (after the closing `</p>` of the profession line, i.e. right before the `</div>` that closes the left column around line 14), add a tier badge:

```blade
            @php($tier = $user->practitionerTier())
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $tier->tailwindBadge() }}">{{ $tier->label() }}</span>
                @if($tier->next())
                    <span class="text-xs text-slate-500">{{ $tier->description() }}</span>
                @endif
            </div>
```

This uses `$user`, which the dashboard view already receives (the header references `{{ $user->name }}`).

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=PractitionerPortalTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add resources/views/practitioner/dashboard.blade.php tests/Feature/PractitionerPortalTest.php
git commit -m "feat(practitioner): show tier badge on practitioner dashboard"
```

---

## Task 6: `scopeByTierPriority` on PractitionerApplication

**Files:**
- Modify: `app/Models/PractitionerApplication.php`
- Test: `tests/Feature/PractitionerPortalTest.php`

This scope reproduces tier ordering at the DB level (Fellow → Distinguished → Verified → Associate) without a stored column. Tier ordering is monotonic in `(is_verified, published_findings_count)`, so we order by verification first, then published-findings count, then recency.

- [ ] **Step 1: Write the failing test**

Add this test method inside `PractitionerPortalTest`:

```php
public function test_applications_sort_by_tier_priority(): void
{
    $program = PractitionerProgram::factory()->paid()->create();

    // Fellow: verified + 8 published findings
    $fellow = $this->practitioner();
    $fellow->practitionerProfile->update(['is_verified' => true]);
    PractitionerFinding::factory()->published()->count(8)->create(['practitioner_id' => $fellow->id]);

    // Verified: verified + 0 findings
    $verified = $this->practitioner();
    $verified->practitionerProfile->update(['is_verified' => true]);

    // Distinguished: verified + 3 published findings
    $distinguished = $this->practitioner();
    $distinguished->practitionerProfile->update(['is_verified' => true]);
    PractitionerFinding::factory()->published()->count(3)->create(['practitioner_id' => $distinguished->id]);

    foreach ([$verified, $fellow, $distinguished] as $u) {
        PractitionerApplication::factory()->create([
            'practitioner_id' => $u->id,
            'program_id'      => $program->id,
        ]);
    }

    $ordered = PractitionerApplication::where('program_id', $program->id)
        ->byTierPriority()
        ->pluck('practitioner_id')
        ->all();

    $this->assertSame([$fellow->id, $distinguished->id, $verified->id], $ordered);
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_applications_sort_by_tier_priority`
Expected: FAIL — `Call to undefined method ...::byTierPriority()`.

- [ ] **Step 3: Write minimal implementation**

In `app/Models/PractitionerApplication.php`, add this scope method inside the class (alongside the existing relations/scopes). It joins the practitioner's profile for verification and uses a correlated subquery for the published-findings count:

```php
public function scopeByTierPriority($query)
{
    return $query
        ->select('practitioner_applications.*')
        ->leftJoin('practitioner_profiles', 'practitioner_profiles.user_id', '=', 'practitioner_applications.practitioner_id')
        ->orderByDesc('practitioner_profiles.is_verified')
        ->orderByDesc(
            \App\Models\PractitionerFinding::selectRaw('count(*)')
                ->whereColumn('practitioner_findings.practitioner_id', 'practitioner_applications.practitioner_id')
                ->where('practitioner_findings.is_published', true)
        )
        ->orderBy('practitioner_applications.created_at');
}
```

> The `select('practitioner_applications.*')` keeps the model hydrating from its own table despite the join. Ordering by `is_verified` desc then published-findings count desc yields exactly the tier order, because Fellow(verified,≥8) > Distinguished(verified,3-7) > Verified(verified,<3) > Associate(unverified).

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=PractitionerPortalTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Models/PractitionerApplication.php tests/Feature/PractitionerPortalTest.php
git commit -m "feat(practitioner): add tier-priority ordering scope for applications"
```

---

## Task 7: Filament — tier columns + priority sort

**Files:**
- Modify: `app/Filament/Resources/PractitionerProfileResource.php` (table columns ~line 91-118)
- Modify: `app/Filament/Resources/PractitionerApplicationResource.php` (table ~line 58-82, getEloquentQuery ~line 168)
- Test: `tests/Feature/PractitionerPortalTest.php`

Filament column rendering is exercised by loading the admin list pages as an authenticated admin. Use the existing admin/role setup pattern.

- [ ] **Step 1: Write the failing test**

The Filament panel is mounted at path `admin`, and `User::canAccessPanel()` admits `super_admin`. `RolePermissionSeeder` (run in `setUp()`) creates the `super_admin` role. Resource list URLs use Filament's default kebab-plural slugs: `/admin/practitioner-profiles` and `/admin/practitioner-applications`. Add this helper and tests inside `PractitionerPortalTest`:

```php
private function admin(): User
{
    $user = User::factory()->create();
    $user->assignRole('super_admin');
    return $user;
}

public function test_admin_can_load_practitioner_profiles_list_with_tier_column(): void
{
    $admin = $this->admin();
    $practitioner = $this->practitioner();
    $practitioner->practitionerProfile->update(['is_verified' => true]);

    $this->actingAs($admin)
        ->get('/admin/practitioner-profiles')
        ->assertOk()
        ->assertSee('Tier');
}

public function test_admin_can_load_practitioner_applications_list(): void
{
    $admin = $this->admin();
    $practitioner = $this->practitioner();
    PractitionerApplication::factory()->create(['practitioner_id' => $practitioner->id]);

    $this->actingAs($admin)
        ->get('/admin/practitioner-applications')
        ->assertOk()
        ->assertSee('Tier');
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter="test_admin_can_load_practitioner"`
Expected: FAIL — pages load but do not contain a "Tier" column header.

- [ ] **Step 3: Write minimal implementation**

In `app/Filament/Resources/PractitionerProfileResource.php`, add a `use App\Enums\PractitionerTier;` import, and add a Tier badge column to the `->columns([...])` array (after the `is_verified` IconColumn around line 108):

```php
                Tables\Columns\TextColumn::make('tier')
                    ->label('Tier')
                    ->badge()
                    ->state(fn (PractitionerProfile $record): string => $record->user->practitionerTier()->label())
                    ->color(fn (PractitionerProfile $record): string => $record->user->practitionerTier()->filamentColor()),
```

In `app/Filament/Resources/PractitionerApplicationResource.php`, add a `use App\Enums\PractitionerTier;` import, and add a Tier badge column to the `->columns([...])` array (after the `program.title` column around line 63):

```php
                Tables\Columns\TextColumn::make('tier')
                    ->label('Tier')
                    ->badge()
                    ->state(fn (PractitionerApplication $record): string => $record->practitioner->practitionerTier()->label())
                    ->color(fn (PractitionerApplication $record): string => $record->practitioner->practitionerTier()->filamentColor()),
```

Then change the default sort to tier priority. Replace `->defaultSort('created_at', 'desc')` (line 82) with:

```php
            ->modifyQueryUsing(fn ($query) => $query->byTierPriority())
```

> `byTierPriority()` (from Task 6) already includes a deterministic final `orderBy created_at`, so removing `defaultSort` is correct — keeping both would let Filament's default sort override the scope.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter="test_admin_can_load_practitioner"`
Expected: PASS.

- [ ] **Step 5: Run the full suite**

Run: `php artisan test`
Expected: PASS — all prior tests (118 baseline) plus the new tier tests green.

- [ ] **Step 6: Commit**

```bash
git add app/Filament/Resources/PractitionerProfileResource.php app/Filament/Resources/PractitionerApplicationResource.php tests/Feature/PractitionerPortalTest.php
git commit -m "feat(practitioner): surface tier badges and priority sort in admin panel"
```

---

## Done-When

- Associate (unverified) is blocked (403) from paid programs but can apply to volunteer programs.
- Verified+ can apply to paid programs.
- Tier computes correctly at boundaries (0/2 → Verified, 3/7 → Distinguished, 8+ → Fellow); unpublished findings and unverified status never promote.
- Practitioner dashboard shows the current tier badge.
- Filament profile + application lists show a Tier badge; applications default-sort by tier priority.
- `php artisan test` is fully green.
