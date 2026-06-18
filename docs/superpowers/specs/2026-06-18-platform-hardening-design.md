# Platform Production Hardening — Design Spec

> **For agentic workers:** REQUIRED SUB-SKILL: Use `superpowers:subagent-driven-development` (recommended) or `superpowers:executing-plans` to implement this plan task-by-task.

**Goal:** Close all identified RBAC, authorization, validation, and role-profile gaps to reach 100% production readiness across the OPES Health Systems platform.

**Audit basis:** Parallel codebase scan conducted 2026-06-18 across RBAC/Filament resources, account-type models, test coverage, input validation, and authorization patterns.

**Architecture:** 4 sequential tracks, each self-contained with its own migrations, tests, and commits. No track depends on another completing first. Execution order: Track 1 → 2 → 3 → 4.

**Tech stack:** Laravel 13.8 / PHP 8.3 / Spatie Permission v8 / Filament v3 / Blade + Tailwind / SQLite (tests) / MySQL (production)

---

## Track 1 — RBAC / Filament Hardening

### Problem

Three Filament admin resources have no `canAccess()` override and therefore default to allowing any authenticated user who can enter the Filament panel (currently `super_admin`, `admin`, `support`). Support staff should not be able to view or modify demo requests, tester applications, or partner applications. Additionally, the Confidential route group incorrectly permits the `support` role to access investor and financial documents.

### Scope

**Task 1.1 — Fix 3 missing `canAccess()` in Filament resources**

Files:
- `app/Filament/Resources/DemoRequestResource.php`
- `app/Filament/Resources/TesterApplicationResource.php`
- `app/Filament/Resources/PartnerApplicationResource.php`

Each resource gets:
```php
public static function canAccess(): bool
{
    return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
}
```

Support users retain panel access (for TicketResource) but are excluded from these three sensitive resources.

**Task 1.2 — Restrict Confidential route group to admin only**

File: `routes/web.php`

The route group serving strategy docs, financial model, risk assessment, sales playbook, government proposal, and investor pitch currently allows `role:super_admin|admin|support`. Support staff should not access investor/financial documents.

Change middleware from:
```php
->middleware(['auth', 'role:super_admin|admin|support'])
```
To:
```php
->middleware(['auth', 'role:super_admin|admin'])
```

**Task 1.3 — Lock down read-only Filament resources**

Ensure the following resources cannot be created, edited, or deleted via the UI (they are system-managed or audit records):
- `AuditLogResource` — already has `canCreate(): false`; confirm `canEdit()` and `canDelete()` also return false
- `RoleResource` — add `canCreate(): false` and `canDelete(): false`; roles are seeded, not UI-managed
- Any resource where records are created programmatically (e.g. via Observer/Event) and should not be manually edited

### Tests

- Support user: assert 403 on DemoRequestResource, TesterApplicationResource, PartnerApplicationResource
- Admin user: assert 200 on all three
- Support user: assert 403 on `/en/confidential/*` routes
- Admin user: assert 200 on confidential routes

---

## Track 2 — Laravel Policy Classes

### Problem

Authorization is currently enforced via two mechanisms: (1) role middleware at the route group level and (2) inline `abort_if()` / `abort_unless()` checks in individual controller methods. This is functional but has two weaknesses:

- Ownership checks (e.g. "can this customer view *this specific* ticket?") are duplicated across controller methods with no single source of truth
- No standard 403 response shape — `abort_if` and `abort(403)` behave identically but produce inconsistent stack traces and are harder to test uniformly

Laravel Policies provide a single, testable class per model that defines all authorization rules, registered once and invoked via `$this->authorize()` in controllers.

### Policies to Create

**`TicketPolicy`**
```php
// Governs Customer\TicketController and Support\TicketController
view(User $user, Ticket $ticket): bool
    → $user->id === $ticket->user_id || $user->hasAnyRole(['support', 'admin', 'super_admin'])

update(User $user, Ticket $ticket): bool
    → same as view

reply(User $user, Ticket $ticket): bool
    → $user->id === $ticket->user_id || $user->hasAnyRole(['support', 'admin', 'super_admin'])

updateStatus(User $user, Ticket $ticket): bool
    → $user->hasAnyRole(['support', 'admin', 'super_admin'])

assign(User $user, Ticket $ticket): bool
    → $user->hasAnyRole(['support', 'admin', 'super_admin'])
```

**`TesterAssignmentPolicy`**
```php
// Governs Tester\AssignmentController and Tester\BugReportController
view(User $user, TesterAssignment $assignment): bool
    → $user->id === $assignment->assigned_to || $user->hasAnyRole(['admin', 'super_admin'])

update(User $user, TesterAssignment $assignment): bool
    → $user->id === $assignment->assigned_to || $user->hasAnyRole(['admin', 'super_admin'])
```

**`PractitionerApplicationPolicy`**
```php
// Governs Practitioner\ApplicationController
view(User $user, PractitionerApplication $application): bool
    → $user->id === $application->practitioner_id || $user->hasAnyRole(['admin', 'super_admin'])

update(User $user, PractitionerApplication $application): bool
    → $user->hasAnyRole(['admin', 'super_admin'])  // practitioners cannot edit submitted applications
```

**`PractitionerFindingPolicy`**
```php
// Governs Practitioner\FindingController
view(User $user, PractitionerFinding $finding): bool
    → $user->id === $finding->practitioner_id || $user->hasAnyRole(['admin', 'super_admin'])

create(User $user): bool
    → $user->hasRole('practitioner')

update(User $user, PractitionerFinding $finding): bool
    → $user->id === $finding->practitioner_id && !$finding->is_published
      // Published findings are immutable

delete(User $user, PractitionerFinding $finding): bool
    → $user->id === $finding->practitioner_id && !$finding->is_published
      || $user->hasAnyRole(['admin', 'super_admin'])
```

**`DocumentPolicy`**
```php
// Governs document download routes
view(User $user, Document $document): bool
    → $user->id === $document->user_id || $user->hasAnyRole(['admin', 'super_admin'])

download(User $user, Document $document): bool
    → same as view
```

### Registration

In `app/Providers/AppServiceProvider.php` `boot()` method (Laravel 13 — no separate AuthServiceProvider):

```php
use Illuminate\Support\Facades\Gate;

Gate::policy(Ticket::class, TicketPolicy::class);
Gate::policy(TesterAssignment::class, TesterAssignmentPolicy::class);
Gate::policy(PractitionerApplication::class, PractitionerApplicationPolicy::class);
Gate::policy(PractitionerFinding::class, PractitionerFindingPolicy::class);
Gate::policy(Document::class, DocumentPolicy::class);
```

### Controller Wiring

Replace inline `abort_if()` ownership checks with `$this->authorize()`:

```php
// Before:
abort_if((int) $ticket->user_id !== $user->id, 403);

// After:
$this->authorize('view', $ticket);
```

### Tests

Each Policy gets a dedicated test asserting:
- Owner can perform allowed actions
- Non-owner of same role is denied
- Admin/super_admin can perform all actions
- Published findings cannot be updated/deleted by practitioner

---

## Track 3 — Validation Hardening

### Problem

Two controller pairs accept user input without fully validating it against the domain model's constraints.

### Task 3.1 — Survey Submission Validation

**Files:**
- `app/Http/Controllers/Customer/SurveyController.php`
- `app/Http/Controllers/Practitioner/SurveyController.php`

**Current behaviour:** The `submit()` method loops over `$survey->questions`, reads `$request->input("q_{$question->id}")` and casts it with `(int)` or direct assignment — no bounds check, no enum validation, no required-field enforcement.

**Fix:** Before the loop, build a dynamic `$rules` array keyed by `"q_{$question->id}"` based on question type:

```php
$rules = [];
foreach ($survey->questions as $question) {
    $key = "q_{$question->id}";
    $rules[$key] = match ($question->type) {
        'rating'          => 'required|integer|min:1|max:5',
        'multiple_choice' => 'required|string|in:' . implode(',', (array) $question->options),
        'yes_no'          => 'required|string|in:yes,no',
        default           => 'nullable|string|max:2000',
    };
}
$request->validate($rules);
```

The existing loop that creates `SurveyAnswer` rows runs unchanged after validation passes.

**Edge cases:**
- `$question->options` may be stored as JSON array — cast to array before `implode`
- A survey with zero questions must still process without error (empty `$rules` → `validate([])` passes)
- Unanswered optional (text) questions: `nullable` allows missing values

### Task 3.2 — Blog Query Parameter Validation

**File:** `app/Http/Controllers/BlogController.php`

**Current behaviour:** `category` and `search` are read from query string without validation. `category` is used in a WHERE clause; `search` in a LIKE. Neither is bounded.

**Fix:**

```php
$validated = $request->validate([
    'category' => ['nullable', 'string', Rule::in(array_keys(BlogPost::categoryOptions()))],
    'search'   => 'nullable|string|max:255',
]);

$activeCategory = $validated['category'] ?? null;
$search = $validated['search'] ?? '';
```

If `BlogPost::categoryOptions()` does not exist, use the distinct DB query pattern:
```php
Rule::in(BlogPost::distinct()->orderBy('category')->pluck('category')->filter()->toArray())
```

### Tests

- Survey: submit with out-of-bounds rating (6) → 422
- Survey: submit multiple_choice with invalid option → 422
- Survey: submit valid form → 200/redirect
- Blog: `?category=nonexistent` → 422 (or graceful empty results if category is not validated — confirm behaviour)
- Blog: `?search=` + 300 chars → 422

---

## Track 4 — Role Profile Tables

### Problem

Five portal roles (Tester, Manager, Support, Accountant, HR) have no role-specific profile tables. Currently:
- Tester profile page edits `User.name` and `User.phone` only
- Manager, HR, Support, Accountant portals have no profile pages at all

This makes it impossible to store role-specific professional data (testing specialty, management level, ticket specialization, etc.) and leaves 4 portals without a standard profile feature present in all other portals.

### New Tables

**`tester_profiles`**
```
id, user_id (FK→users, unique), testing_specialty (enum: web,mobile,api,desktop),
device_types (text, nullable), portfolio_url (nullable), certifications (text, nullable),
availability_notes (text, nullable), bio (text, nullable),
timestamps
```

**`manager_profiles`**
```
id, user_id (FK→users, unique), management_level (enum: team_lead,senior_manager,director),
department_id (FK→departments, nullable), bio (text, nullable),
timestamps
```

**`support_profiles`**
```
id, user_id (FK→users, unique),
ticket_specialization (enum: technical,billing,general,all, default: all),
shift (enum: morning,afternoon,evening, nullable),
languages (text, nullable), bio (text, nullable),
timestamps
```

**`accountant_profiles`**
```
id, user_id (FK→users, unique),
accounting_specialization (enum: tax,payroll,audit,general, default: general),
certifications (text, nullable), bio (text, nullable),
timestamps
```

*Note: HR users are employees and already have full `EmployeeProfile` rows. No separate HR profile table is needed — the HR portal profile page will display/edit `EmployeeProfile` data.*

### Models

Each model follows the same pattern as `PractitionerProfile`:
- `$fillable` covering all columns except `id`, `user_id`, `timestamps`
- `$casts` for enum columns
- `user()` BelongsTo relationship
- No `$hidden` required (no sensitive payment fields)

`User` model additions:
```php
public function testerProfile(): HasOne { return $this->hasOne(TesterProfile::class); }
public function managerProfile(): HasOne { return $this->hasOne(ManagerProfile::class); }
public function supportProfile(): HasOne { return $this->hasOne(SupportProfile::class); }
public function accountantProfile(): HasOne { return $this->hasOne(AccountantProfile::class); }
```

### Controllers & Routes

Each portal gets a `ProfileController` with `show()` and `update()` methods, following the existing `Tester\ProfileController` pattern:

```
GET  /{locale}/tester/profile      → Tester\ProfileController@show
PATCH /{locale}/tester/profile     → Tester\ProfileController@update  (already exists — extend)

GET  /{locale}/manager/profile     → Manager\ProfileController@show
PATCH /{locale}/manager/profile    → Manager\ProfileController@update

GET  /{locale}/support/profile     → Support\ProfileController@show
PATCH /{locale}/support/profile    → Support\ProfileController@update

GET  /{locale}/hr/profile          → HR\ProfileController@show
PATCH /{locale}/hr/profile         → HR\ProfileController@update

GET  /{locale}/accountant/profile  → Accountant\ProfileController@show
PATCH /{locale}/accountant/profile → Accountant\ProfileController@update
```

### Views

Each portal profile view follows the established pattern:
- `<x-layouts.X>` layout matching the portal's nav
- Avatar card (initials + gradient, name, email, role badge, member-since)
- Edit form: `User.name` (required), `User.phone` (optional), role-specific fields, Save button
- Flash success message on redirect

The existing `resources/views/tester/profile.blade.php` is extended to include `TesterProfile` fields below the current User fields.

### Navigation Updates

Each portal layout (`resources/views/components/layouts/X.blade.php`) gets a "My Profile" nav link added, consistent with the Tester and Practitioner layouts that already have it.

### Tests

Per portal:
- Profile page loads (assertOk, assertSee user name)
- Profile update saves User fields + role-specific profile fields (assertDatabaseHas both tables)
- Non-role user gets 403

---

## Success Criteria

| Area | Metric |
|---|---|
| Filament RBAC | 0 resources missing `canAccess()` |
| Confidential routes | Support role cannot access `/confidential/*` |
| Policy coverage | 5 Policy classes registered, all controllers use `authorize()` |
| Survey validation | Invalid ratings/choices return 422 |
| Blog validation | Invalid category/overlong search return 422 |
| Profile completeness | All 7 non-admin portals have a working profile page |
| Test suite | 266+ tests, 0 failures |
