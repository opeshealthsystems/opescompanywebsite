# Platform Production Hardening — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use `superpowers:subagent-driven-development` (recommended) or `superpowers:executing-plans` to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Close all RBAC, authorization, validation, and role-profile gaps across the OPES Health Systems platform to reach 100% production readiness.

**Architecture:** 4 sequential tracks executed task-by-task: (1) Filament/RBAC hardening, (2) Laravel Policy classes, (3) Input validation, (4) Role profile tables. Each task is self-contained with its own migration (if any), tests, and commit.

**Tech Stack:** Laravel 13.8 / PHP 8.3 / Spatie Permission v8 / Filament v3 / Blade + Tailwind / SQLite (tests) / MySQL (production)

**PHP binary:** `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`

**Run tests with:** `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test`

**Current test count:** 266 passing, 0 failures — must stay green after every commit.

**Seeding in tests:** Every Feature test `setUp()` must call `$this->seed(\Database\Seeders\RolePermissionSeeder::class);` before using roles.

---

## Track 1 — RBAC / Filament Hardening

---

### Task 1: RBAC / Filament Hardening

**Files:**
- Modify: `app/Filament/Resources/DemoRequestResource.php`
- Modify: `app/Filament/Resources/TesterApplicationResource.php`
- Modify: `app/Filament/Resources/PartnerApplicationResource.php`
- Modify: `app/Filament/Resources/AuditLogResource.php` (add canEdit/canDelete)
- Modify: `routes/web.php` (confidential middleware)
- Create: `tests/Feature/RbacHardeningTest.php`

---

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/RbacHardeningTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    public function test_support_cannot_access_demo_request_resource(): void
    {
        $support = $this->makeUser('support');
        $this->actingAs($support);
        $this->assertFalse(\App\Filament\Resources\DemoRequestResource::canAccess());
    }

    public function test_admin_can_access_demo_request_resource(): void
    {
        $admin = $this->makeUser('admin');
        $this->actingAs($admin);
        $this->assertTrue(\App\Filament\Resources\DemoRequestResource::canAccess());
    }

    public function test_support_cannot_access_tester_application_resource(): void
    {
        $support = $this->makeUser('support');
        $this->actingAs($support);
        $this->assertFalse(\App\Filament\Resources\TesterApplicationResource::canAccess());
    }

    public function test_support_cannot_access_partner_application_resource(): void
    {
        $support = $this->makeUser('support');
        $this->actingAs($support);
        $this->assertFalse(\App\Filament\Resources\PartnerApplicationResource::canAccess());
    }

    public function test_support_cannot_access_confidential_routes(): void
    {
        $support = $this->makeUser('support');
        $response = $this->actingAs($support)->get('/en/strategy');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_confidential_routes(): void
    {
        $admin = $this->makeUser('admin');
        $response = $this->actingAs($admin)->get('/en/strategy');
        $response->assertStatus(200);
    }

    public function test_audit_log_resource_cannot_be_edited(): void
    {
        $model = new \App\Models\AuditLog();
        $admin = $this->makeUser('admin');
        $this->actingAs($admin);
        $this->assertFalse(\App\Filament\Resources\AuditLogResource::canEdit($model));
    }

    public function test_audit_log_resource_cannot_be_deleted(): void
    {
        $model = new \App\Models\AuditLog();
        $admin = $this->makeUser('admin');
        $this->actingAs($admin);
        $this->assertFalse(\App\Filament\Resources\AuditLogResource::canDelete($model));
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=RbacHardeningTest
```

Expected: 5–8 failures (canAccess, route 403, canEdit/canDelete).

- [ ] **Step 3: Add `canAccess()` to DemoRequestResource**

In `app/Filament/Resources/DemoRequestResource.php`, add after the static property declarations (before `form()`):

```php
public static function canAccess(): bool
{
    return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
}
```

- [ ] **Step 4: Add `canAccess()` to TesterApplicationResource**

In `app/Filament/Resources/TesterApplicationResource.php`, add after the static property declarations:

```php
public static function canAccess(): bool
{
    return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
}
```

- [ ] **Step 5: Add `canAccess()` to PartnerApplicationResource**

In `app/Filament/Resources/PartnerApplicationResource.php`, add after the static property declarations:

```php
public static function canAccess(): bool
{
    return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
}
```

- [ ] **Step 6: Add `canEdit()` and `canDelete()` to AuditLogResource**

In `app/Filament/Resources/AuditLogResource.php`, after `canCreate()`:

```php
public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool { return false; }
public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool { return false; }
```

- [ ] **Step 7: Restrict confidential routes to admin only**

In `routes/web.php`, find this line:

```php
Route::middleware(['auth', 'role:admin|super_admin|support'])
```

(It's the line just before the confidential route group with strategy/risk/financial-model routes)

Change it to:

```php
Route::middleware(['auth', 'role:admin|super_admin'])
```

- [ ] **Step 8: Run tests to confirm all pass**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=RbacHardeningTest
```

Expected: all pass.

- [ ] **Step 9: Run full suite**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 274+ passing, 0 failures.

- [ ] **Step 10: Commit**

```
git add app/Filament/Resources/DemoRequestResource.php app/Filament/Resources/TesterApplicationResource.php app/Filament/Resources/PartnerApplicationResource.php app/Filament/Resources/AuditLogResource.php routes/web.php tests/Feature/RbacHardeningTest.php
git commit -m "feat(rbac): restrict Filament resources and confidential routes to admin roles"
```

---

## Track 2 — Laravel Policy Classes

---

### Task 2: TicketPolicy

**Files:**
- Create: `app/Policies/TicketPolicy.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `app/Http/Controllers/Customer/TicketController.php`
- Create: `tests/Feature/TicketPolicyTest.php`

---

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/TicketPolicyTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    public function test_owner_can_view_own_ticket(): void
    {
        $customer = $this->makeUser('customer');
        $ticket = Ticket::factory()->create(['user_id' => $customer->id]);
        $this->assertTrue($customer->can('view', $ticket));
    }

    public function test_non_owner_customer_cannot_view_ticket(): void
    {
        $owner = $this->makeUser('customer');
        $other = $this->makeUser('customer');
        $ticket = Ticket::factory()->create(['user_id' => $owner->id]);
        $this->assertFalse($other->can('view', $ticket));
    }

    public function test_support_can_view_any_ticket(): void
    {
        $owner = $this->makeUser('customer');
        $support = $this->makeUser('support');
        $ticket = Ticket::factory()->create(['user_id' => $owner->id]);
        $this->assertTrue($support->can('view', $ticket));
    }

    public function test_support_can_update_status(): void
    {
        $owner = $this->makeUser('customer');
        $support = $this->makeUser('support');
        $ticket = Ticket::factory()->create(['user_id' => $owner->id]);
        $this->assertTrue($support->can('updateStatus', $ticket));
    }

    public function test_customer_cannot_update_status(): void
    {
        $customer = $this->makeUser('customer');
        $ticket = Ticket::factory()->create(['user_id' => $customer->id]);
        $this->assertFalse($customer->can('updateStatus', $ticket));
    }

    public function test_customer_ticket_show_uses_policy(): void
    {
        $owner = $this->makeUser('customer');
        $other = $this->makeUser('customer');
        $ticket = Ticket::factory()->create(['user_id' => $owner->id, 'status' => 'open']);

        $response = $this->actingAs($other)->get("/en/customer/tickets/{$ticket->id}");
        $response->assertStatus(403);
    }

    public function test_owner_can_view_ticket_page(): void
    {
        $owner = $this->makeUser('customer');
        $ticket = Ticket::factory()->create(['user_id' => $owner->id, 'status' => 'open']);

        $response = $this->actingAs($owner)->get("/en/customer/tickets/{$ticket->id}");
        $response->assertStatus(200);
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=TicketPolicyTest
```

Expected: failures because TicketPolicy doesn't exist.

- [ ] **Step 3: Create `app/Policies/TicketPolicy.php`**

```php
<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->id === (int) $ticket->user_id
            || $user->hasAnyRole(['support', 'admin', 'super_admin']);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $this->view($user, $ticket);
    }

    public function reply(User $user, Ticket $ticket): bool
    {
        return $this->view($user, $ticket);
    }

    public function updateStatus(User $user, Ticket $ticket): bool
    {
        return $user->hasAnyRole(['support', 'admin', 'super_admin']);
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->hasAnyRole(['support', 'admin', 'super_admin']);
    }
}
```

- [ ] **Step 4: Register policy in AppServiceProvider**

In `app/Providers/AppServiceProvider.php`, add to the `boot()` method:

```php
public function boot(): void
{
    \Illuminate\Support\Facades\Gate::policy(\App\Models\Ticket::class, \App\Policies\TicketPolicy::class);
}
```

- [ ] **Step 5: Wire Customer\TicketController to use policy**

In `app/Http/Controllers/Customer/TicketController.php`:

Replace the `show()` method's inline check:
```php
// BEFORE:
abort_if((int) $ticket->user_id !== $user->id, 403);

// AFTER:
$this->authorize('view', $ticket);
```

Replace the `reply()` method's inline check (the first `abort_if` about user_id, NOT the one about `isOpen()`):
```php
// BEFORE:
abort_if((int) $ticket->user_id !== $user->id, 403);

// AFTER:
$this->authorize('reply', $ticket);
```

Note: keep the `abort_unless($ticket->isOpen(), 403, ...)` line in `reply()` as-is since that's a business rule, not an ownership check.

- [ ] **Step 6: Run tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=TicketPolicyTest
```

Expected: all pass.

- [ ] **Step 7: Run full suite**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 281+ passing, 0 failures.

- [ ] **Step 8: Commit**

```
git add app/Policies/TicketPolicy.php app/Providers/AppServiceProvider.php app/Http/Controllers/Customer/TicketController.php tests/Feature/TicketPolicyTest.php
git commit -m "feat(policy): add TicketPolicy and wire Customer\TicketController"
```

---

### Task 3: TesterAssignmentPolicy

**Files:**
- Create: `app/Policies/TesterAssignmentPolicy.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `app/Http/Controllers/Tester/AssignmentController.php`
- Create: `tests/Feature/TesterAssignmentPolicyTest.php`

---

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/TesterAssignmentPolicyTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\TesterAssignment;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesterAssignmentPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    public function test_assigned_tester_can_view_assignment(): void
    {
        $tester = $this->makeUser('tester');
        $assignment = TesterAssignment::factory()->create(['assigned_to' => $tester->id]);
        $this->assertTrue($tester->can('view', $assignment));
    }

    public function test_other_tester_cannot_view_assignment(): void
    {
        $tester = $this->makeUser('tester');
        $other  = $this->makeUser('tester');
        $assignment = TesterAssignment::factory()->create(['assigned_to' => $tester->id]);
        $this->assertFalse($other->can('view', $assignment));
    }

    public function test_admin_can_view_any_assignment(): void
    {
        $tester = $this->makeUser('tester');
        $admin  = $this->makeUser('admin');
        $assignment = TesterAssignment::factory()->create(['assigned_to' => $tester->id]);
        $this->assertTrue($admin->can('view', $assignment));
    }

    public function test_assignment_show_uses_policy(): void
    {
        $tester = $this->makeUser('tester');
        $other  = $this->makeUser('tester');
        $assignment = TesterAssignment::factory()->create(['assigned_to' => $tester->id]);

        $response = $this->actingAs($other)->get("/en/tester/assignments/{$assignment->id}");
        $response->assertStatus(403);
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=TesterAssignmentPolicyTest
```

Expected: failures.

- [ ] **Step 3: Create `app/Policies/TesterAssignmentPolicy.php`**

```php
<?php

namespace App\Policies;

use App\Models\TesterAssignment;
use App\Models\User;

class TesterAssignmentPolicy
{
    public function view(User $user, TesterAssignment $assignment): bool
    {
        return $user->id === (int) $assignment->assigned_to
            || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function update(User $user, TesterAssignment $assignment): bool
    {
        return $this->view($user, $assignment);
    }
}
```

- [ ] **Step 4: Register policy in AppServiceProvider**

In `app/Providers/AppServiceProvider.php`, add to the `boot()` method (already has TicketPolicy from Task 2):

```php
\Illuminate\Support\Facades\Gate::policy(\App\Models\TesterAssignment::class, \App\Policies\TesterAssignmentPolicy::class);
```

- [ ] **Step 5: Wire AssignmentController**

In `app/Http/Controllers/Tester/AssignmentController.php`:

In `show()`, replace:
```php
abort_if((int) $assignment->assigned_to !== $user->id, 403);
```
with:
```php
$this->authorize('view', $assignment);
```

In `updateStatus()`, replace:
```php
abort_if((int) $assignment->assigned_to !== $user->id, 403);
```
with:
```php
$this->authorize('update', $assignment);
```

In `storeBugReport()`, replace:
```php
abort_if((int) $assignment->assigned_to !== $user->id, 403);
```
with:
```php
$this->authorize('update', $assignment);
```

Keep all other `abort_unless` lines unchanged (business-rule guards).

- [ ] **Step 6: Run tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=TesterAssignmentPolicyTest
```

Expected: all pass.

- [ ] **Step 7: Run full suite**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 285+ passing, 0 failures.

- [ ] **Step 8: Commit**

```
git add app/Policies/TesterAssignmentPolicy.php app/Providers/AppServiceProvider.php app/Http/Controllers/Tester/AssignmentController.php tests/Feature/TesterAssignmentPolicyTest.php
git commit -m "feat(policy): add TesterAssignmentPolicy and wire AssignmentController"
```

---

### Task 4: PractitionerApplicationPolicy, PractitionerFindingPolicy, DocumentPolicy

**Files:**
- Create: `app/Policies/PractitionerApplicationPolicy.php`
- Create: `app/Policies/PractitionerFindingPolicy.php`
- Create: `app/Policies/DocumentPolicy.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `app/Http/Controllers/Practitioner/ApplicationController.php`
- Modify: `app/Http/Controllers/Practitioner/FindingController.php`
- Create: `tests/Feature/PractitionerPolicyTest.php`

**Note on Documents:** Check if `App\Models\Document` exists with `php artisan model:show Document 2>&1`. If it doesn't exist, skip DocumentPolicy (create a stub with a TODO comment and don't register it).

---

- [ ] **Step 1: Check if Document model exists**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan model:show Document 2>&1
```

If the output says "model not found" or similar error, Document model does not exist — skip DocumentPolicy for now.

- [ ] **Step 2: Write the failing tests**

Create `tests/Feature/PractitionerPolicyTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PractitionerPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    // --- PractitionerApplicationPolicy ---

    public function test_practitioner_can_view_own_application(): void
    {
        $practitioner = $this->makeUser('practitioner');
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $practitioner->id]);
        $this->assertTrue($practitioner->can('view', $app));
    }

    public function test_practitioner_cannot_view_others_application(): void
    {
        $p1 = $this->makeUser('practitioner');
        $p2 = $this->makeUser('practitioner');
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $p1->id]);
        $this->assertFalse($p2->can('view', $app));
    }

    public function test_admin_can_view_any_application(): void
    {
        $practitioner = $this->makeUser('practitioner');
        $admin = $this->makeUser('admin');
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $practitioner->id]);
        $this->assertTrue($admin->can('view', $app));
    }

    // --- PractitionerFindingPolicy ---

    public function test_practitioner_can_update_unpublished_finding(): void
    {
        $practitioner = $this->makeUser('practitioner');
        $finding = PractitionerFinding::factory()->create([
            'practitioner_id' => $practitioner->id,
            'is_published'    => false,
        ]);
        $this->assertTrue($practitioner->can('update', $finding));
    }

    public function test_practitioner_cannot_update_published_finding(): void
    {
        $practitioner = $this->makeUser('practitioner');
        $finding = PractitionerFinding::factory()->create([
            'practitioner_id' => $practitioner->id,
            'is_published'    => true,
        ]);
        $this->assertFalse($practitioner->can('update', $finding));
    }

    public function test_other_practitioner_cannot_update_finding(): void
    {
        $p1 = $this->makeUser('practitioner');
        $p2 = $this->makeUser('practitioner');
        $finding = PractitionerFinding::factory()->create([
            'practitioner_id' => $p1->id,
            'is_published'    => false,
        ]);
        $this->assertFalse($p2->can('update', $finding));
    }

    public function test_application_show_uses_policy(): void
    {
        $p1 = $this->makeUser('practitioner');
        $p2 = $this->makeUser('practitioner');
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $p1->id]);

        $response = $this->actingAs($p2)->get("/en/practitioner/applications/{$app->id}");
        $response->assertStatus(403);
    }
}
```

- [ ] **Step 3: Run tests to confirm they fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=PractitionerPolicyTest
```

Expected: failures.

- [ ] **Step 4: Create `app/Policies/PractitionerApplicationPolicy.php`**

```php
<?php

namespace App\Policies;

use App\Models\PractitionerApplication;
use App\Models\User;

class PractitionerApplicationPolicy
{
    public function view(User $user, PractitionerApplication $application): bool
    {
        return $user->id === (int) $application->practitioner_id
            || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function update(User $user, PractitionerApplication $application): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }
}
```

- [ ] **Step 5: Create `app/Policies/PractitionerFindingPolicy.php`**

```php
<?php

namespace App\Policies;

use App\Models\PractitionerFinding;
use App\Models\User;

class PractitionerFindingPolicy
{
    public function view(User $user, PractitionerFinding $finding): bool
    {
        return $user->id === (int) $finding->practitioner_id
            || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('practitioner');
    }

    public function update(User $user, PractitionerFinding $finding): bool
    {
        return $user->id === (int) $finding->practitioner_id
            && ! $finding->is_published;
    }

    public function delete(User $user, PractitionerFinding $finding): bool
    {
        return ($user->id === (int) $finding->practitioner_id && ! $finding->is_published)
            || $user->hasAnyRole(['admin', 'super_admin']);
    }
}
```

- [ ] **Step 6: Register all policies in AppServiceProvider**

In `app/Providers/AppServiceProvider.php`, the `boot()` method should now contain:

```php
public function boot(): void
{
    \Illuminate\Support\Facades\Gate::policy(\App\Models\Ticket::class, \App\Policies\TicketPolicy::class);
    \Illuminate\Support\Facades\Gate::policy(\App\Models\TesterAssignment::class, \App\Policies\TesterAssignmentPolicy::class);
    \Illuminate\Support\Facades\Gate::policy(\App\Models\PractitionerApplication::class, \App\Policies\PractitionerApplicationPolicy::class);
    \Illuminate\Support\Facades\Gate::policy(\App\Models\PractitionerFinding::class, \App\Policies\PractitionerFindingPolicy::class);
}
```

- [ ] **Step 7: Wire Practitioner\ApplicationController**

In `app/Http/Controllers/Practitioner/ApplicationController.php`, in `show()`:

Replace:
```php
abort_unless($application->practitioner_id === auth()->id(), 403);
```
with:
```php
$this->authorize('view', $application);
```

- [ ] **Step 8: Wire Practitioner\FindingController**

In `app/Http/Controllers/Practitioner/FindingController.php`, in `create()`:

Replace the first line:
```php
abort_unless($application->practitioner_id === auth()->id(), 403);
```
with:
```php
$this->authorize('view', $application);
```

In `store()`, replace the first line:
```php
abort_unless($application->practitioner_id === auth()->id(), 403);
```
with:
```php
$this->authorize('view', $application);
```

Keep all remaining `abort_unless` lines (verified practitioner check, approved status check) unchanged.

- [ ] **Step 9: Run tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=PractitionerPolicyTest
```

Expected: all pass.

- [ ] **Step 10: Run full suite**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 292+ passing, 0 failures.

- [ ] **Step 11: Commit**

```
git add app/Policies/PractitionerApplicationPolicy.php app/Policies/PractitionerFindingPolicy.php app/Providers/AppServiceProvider.php app/Http/Controllers/Practitioner/ApplicationController.php app/Http/Controllers/Practitioner/FindingController.php tests/Feature/PractitionerPolicyTest.php
git commit -m "feat(policy): add PractitionerApplication + Finding policies and wire controllers"
```

---

## Track 3 — Validation Hardening

---

### Task 5: Survey Submission Validation

**Files:**
- Modify: `app/Http/Controllers/Customer/SurveyController.php`
- Modify: `app/Http/Controllers/Practitioner/SurveyController.php`
- Create: `tests/Feature/SurveyValidationTest.php`

**Pattern:** Both controllers have an identical `submit()` method. The fix is identical for both.

---

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/SurveyValidationTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    private function makeSurveyWithQuestion(string $audience, string $type, array $options = []): array
    {
        $survey = Survey::factory()->create(['status' => 'active', 'audience' => $audience]);
        $question = SurveyQuestion::factory()->create([
            'survey_id'   => $survey->id,
            'type'        => $type,
            'is_required' => true,
            'options'     => $options ?: null,
        ]);
        return [$survey, $question];
    }

    // Customer survey validation

    public function test_customer_survey_rejects_out_of_bounds_rating(): void
    {
        $customer = $this->makeUser('customer');
        [$survey, $question] = $this->makeSurveyWithQuestion('customers', 'rating');

        $response = $this->actingAs($customer)->post("/en/customer/surveys/{$survey->id}", [
            "q_{$question->id}" => 6,
        ]);
        $response->assertStatus(422);
    }

    public function test_customer_survey_rejects_invalid_multiple_choice(): void
    {
        $customer = $this->makeUser('customer');
        [$survey, $question] = $this->makeSurveyWithQuestion('customers', 'multiple_choice', ['good', 'bad']);

        $response = $this->actingAs($customer)->post("/en/customer/surveys/{$survey->id}", [
            "q_{$question->id}" => 'hacked_value',
        ]);
        $response->assertStatus(422);
    }

    public function test_customer_survey_accepts_valid_rating(): void
    {
        $customer = $this->makeUser('customer');
        [$survey, $question] = $this->makeSurveyWithQuestion('customers', 'rating');

        $response = $this->actingAs($customer)->post("/en/customer/surveys/{$survey->id}", [
            "q_{$question->id}" => 4,
        ]);
        $response->assertRedirect();
        $this->assertNotEquals(422, $response->getStatusCode());
    }

    // Practitioner survey validation

    public function test_practitioner_survey_rejects_out_of_bounds_rating(): void
    {
        $practitioner = $this->makeUser('practitioner');
        [$survey, $question] = $this->makeSurveyWithQuestion('practitioners', 'rating');

        $response = $this->actingAs($practitioner)->post("/en/practitioner/surveys/{$survey->id}", [
            "q_{$question->id}" => 0,
        ]);
        $response->assertStatus(422);
    }

    public function test_practitioner_survey_rejects_invalid_yes_no(): void
    {
        $practitioner = $this->makeUser('practitioner');
        [$survey, $question] = $this->makeSurveyWithQuestion('practitioners', 'yes_no');

        $response = $this->actingAs($practitioner)->post("/en/practitioner/surveys/{$survey->id}", [
            "q_{$question->id}" => 'maybe',
        ]);
        $response->assertStatus(422);
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=SurveyValidationTest
```

Expected: failures (no validation, so 6 passes as a rating).

- [ ] **Step 3: Add validation to Customer\SurveyController**

In `app/Http/Controllers/Customer/SurveyController.php`, in the `submit()` method, add the following block **before** the `foreach ($survey->questions as $question)` loop (after `$response->isSubmitted()` check):

```php
        $rules = [];
        foreach ($survey->questions as $question) {
            $key = "q_{$question->id}";
            $rules[$key] = match ($question->type) {
                'rating'          => 'nullable|integer|min:1|max:5',
                'multiple_choice' => 'nullable|string|in:' . implode(',', (array) ($question->options ?? [])),
                'yes_no'          => 'nullable|string|in:yes,no',
                default           => 'nullable|string|max:2000',
            };
            if ($question->is_required) {
                $rules[$key] = str_replace('nullable', 'required', $rules[$key]);
            }
        }
        $request->validate($rules);
```

- [ ] **Step 4: Add validation to Practitioner\SurveyController**

In `app/Http/Controllers/Practitioner/SurveyController.php`, apply the **exact same block** in `submit()` before the `foreach` loop:

```php
        $rules = [];
        foreach ($survey->questions as $question) {
            $key = "q_{$question->id}";
            $rules[$key] = match ($question->type) {
                'rating'          => 'nullable|integer|min:1|max:5',
                'multiple_choice' => 'nullable|string|in:' . implode(',', (array) ($question->options ?? [])),
                'yes_no'          => 'nullable|string|in:yes,no',
                default           => 'nullable|string|max:2000',
            };
            if ($question->is_required) {
                $rules[$key] = str_replace('nullable', 'required', $rules[$key]);
            }
        }
        $request->validate($rules);
```

- [ ] **Step 5: Run tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=SurveyValidationTest
```

Expected: all pass.

- [ ] **Step 6: Run full suite**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 297+ passing, 0 failures.

- [ ] **Step 7: Commit**

```
git add app/Http/Controllers/Customer/SurveyController.php app/Http/Controllers/Practitioner/SurveyController.php tests/Feature/SurveyValidationTest.php
git commit -m "feat(validation): validate survey answers against question type constraints"
```

---

### Task 6: Blog Query Parameter Validation

**Files:**
- Modify: `app/Http/Controllers/BlogController.php`
- Create: `tests/Feature/BlogValidationTest.php`

---

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/BlogValidationTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_blog_index_accepts_valid_category(): void
    {
        BlogPost::factory()->create(['published' => true, 'category' => 'tech']);
        $response = $this->get('/en/blog?category=tech');
        $response->assertStatus(200);
    }

    public function test_blog_index_rejects_invalid_category(): void
    {
        BlogPost::factory()->create(['published' => true, 'category' => 'tech']);
        $response = $this->get('/en/blog?category=nonexistent_hack');
        $response->assertStatus(422);
    }

    public function test_blog_index_rejects_overlong_search(): void
    {
        $response = $this->get('/en/blog?search=' . str_repeat('a', 256));
        $response->assertStatus(422);
    }

    public function test_blog_index_accepts_valid_search(): void
    {
        $response = $this->get('/en/blog?search=health');
        $response->assertStatus(200);
    }

    public function test_blog_index_loads_without_params(): void
    {
        $response = $this->get('/en/blog');
        $response->assertStatus(200);
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=BlogValidationTest
```

Expected: `test_blog_index_rejects_invalid_category` and `test_blog_index_rejects_overlong_search` fail.

- [ ] **Step 3: Add validation to BlogController**

In `app/Http/Controllers/BlogController.php`, replace the first two lines of `index()`:

```php
// BEFORE:
$activeCategory = $request->query('category');
$search         = trim($request->query('search', ''));
```

```php
// AFTER:
$availableCategories = BlogPost::published()
    ->distinct()
    ->orderBy('category')
    ->pluck('category')
    ->filter()
    ->values()
    ->toArray();

$validated = $request->validate([
    'category' => ['nullable', 'string', \Illuminate\Validation\Rule::in($availableCategories)],
    'search'   => 'nullable|string|max:255',
]);

$activeCategory = $validated['category'] ?? null;
$search         = trim($validated['search'] ?? '');
```

Also add the `Rule` import at the top of the file if not already present:
```php
use Illuminate\Validation\Rule;
```

- [ ] **Step 4: Run tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=BlogValidationTest
```

Expected: all pass.

- [ ] **Step 5: Run full suite**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 302+ passing, 0 failures.

- [ ] **Step 6: Commit**

```
git add app/Http/Controllers/BlogController.php tests/Feature/BlogValidationTest.php
git commit -m "feat(validation): validate blog category and search query parameters"
```

---

## Track 4 — Role Profile Tables

---

### Task 7: TesterProfile Table + Model + Controller Extension

**Files:**
- Create: `database/migrations/XXXX_create_tester_profiles_table.php` (use `php artisan make:migration`)
- Create: `app/Models/TesterProfile.php`
- Modify: `app/Models/User.php`
- Modify: `app/Http/Controllers/Tester/ProfileController.php`
- Modify: `resources/views/tester/profile.blade.php`
- Create: `tests/Feature/TesterProfileTest.php`

---

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/TesterProfileTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesterProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeTester(): User
    {
        $user = User::factory()->create();
        $user->assignRole('tester');
        return $user;
    }

    public function test_tester_profile_page_loads(): void
    {
        $tester = $this->makeTester();
        $response = $this->actingAs($tester)->get("/en/tester/profile");
        $response->assertStatus(200);
        $response->assertSee($tester->name);
    }

    public function test_tester_profile_update_saves_user_fields(): void
    {
        $tester = $this->makeTester();
        $response = $this->actingAs($tester)->patch("/en/tester/profile", [
            'name'  => 'Updated Tester',
            'phone' => '+1234567890',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $tester->id, 'name' => 'Updated Tester']);
    }

    public function test_tester_profile_update_saves_tester_profile_fields(): void
    {
        $tester = $this->makeTester();
        $response = $this->actingAs($tester)->patch("/en/tester/profile", [
            'name'             => $tester->name,
            'testing_specialty'=> 'web',
            'portfolio_url'    => 'https://example.com',
            'bio'              => 'I test web apps.',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('tester_profiles', [
            'user_id'          => $tester->id,
            'testing_specialty'=> 'web',
            'bio'              => 'I test web apps.',
        ]);
    }

    public function test_non_tester_cannot_access_tester_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $response = $this->actingAs($user)->get("/en/tester/profile");
        $response->assertStatus(403);
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=TesterProfileTest
```

Expected: failures (tester_profiles table missing, TesterProfile model missing).

- [ ] **Step 3: Create migration**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan make:migration create_tester_profiles_table
```

Edit the generated file in `database/migrations/`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tester_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('testing_specialty')->nullable(); // web, mobile, api, desktop
            $table->text('device_types')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->text('certifications')->nullable();
            $table->text('availability_notes')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tester_profiles');
    }
};
```

- [ ] **Step 4: Run migration**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan migrate
```

- [ ] **Step 5: Create `app/Models/TesterProfile.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TesterProfile extends Model
{
    protected $fillable = [
        'user_id', 'testing_specialty', 'device_types',
        'portfolio_url', 'certifications', 'availability_notes', 'bio',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function specialtyOptions(): array
    {
        return ['web' => 'Web', 'mobile' => 'Mobile', 'api' => 'API', 'desktop' => 'Desktop'];
    }
}
```

- [ ] **Step 6: Add relationship to User model**

In `app/Models/User.php`, add after the existing profile relationships (e.g. after `practitionerProfile()`):

```php
public function testerProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(\App\Models\TesterProfile::class);
}
```

- [ ] **Step 7: Extend Tester\ProfileController**

Replace the entire `app/Http/Controllers/Tester/ProfileController.php` with:

```php
<?php

namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use App\Models\TesterProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('testerProfile');
        return view('tester.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'phone'             => 'nullable|string|max:30',
            'testing_specialty' => 'nullable|string|in:web,mobile,api,desktop',
            'device_types'      => 'nullable|string|max:1000',
            'portfolio_url'     => 'nullable|url|max:255',
            'certifications'    => 'nullable|string|max:1000',
            'availability_notes'=> 'nullable|string|max:1000',
            'bio'               => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);

        $profileData = array_filter([
            'testing_specialty'  => $validated['testing_specialty'] ?? null,
            'device_types'       => $validated['device_types'] ?? null,
            'portfolio_url'      => $validated['portfolio_url'] ?? null,
            'certifications'     => $validated['certifications'] ?? null,
            'availability_notes' => $validated['availability_notes'] ?? null,
            'bio'                => $validated['bio'] ?? null,
        ], fn($v) => $v !== null);

        TesterProfile::updateOrCreate(['user_id' => $user->id], $profileData);

        return redirect()
            ->route('tester.profile', ['locale' => app()->getLocale()])
            ->with('success', 'Profile updated successfully.');
    }
}
```

- [ ] **Step 8: Extend tester profile view**

In `resources/views/tester/profile.blade.php`, add the following section after the closing `</div>` of the existing edit form div (before `</x-layouts.tester>`):

```blade
{{-- Tester-specific profile fields --}}
<div class="mt-6 bg-slate-900 border border-slate-800 rounded-xl p-6">
    <h2 class="text-sm font-semibold text-white mb-5">Tester Profile</h2>
    <form method="POST" action="{{ route('tester.profile.update', ['locale' => app()->getLocale()]) }}">
        @csrf @method('PATCH')
        <input type="hidden" name="name" value="{{ $user->name }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Testing Specialty</label>
                <select name="testing_specialty" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                    <option value="">— Select —</option>
                    @foreach(\App\Models\TesterProfile::specialtyOptions() as $val => $label)
                        <option value="{{ $val }}" @selected(old('testing_specialty', $user->testerProfile?->testing_specialty) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Portfolio URL</label>
                <input type="url" name="portfolio_url" value="{{ old('portfolio_url', $user->testerProfile?->portfolio_url) }}"
                       class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500"
                       maxlength="255" placeholder="https://...">
                @error('portfolio_url') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Bio</label>
                <textarea name="bio" rows="3" maxlength="2000"
                          class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">{{ old('bio', $user->testerProfile?->bio) }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white"
                    style="background:linear-gradient(135deg,#1A6FE8,#1258c4)">
                Save Tester Profile
            </button>
        </div>
    </form>
</div>
```

- [ ] **Step 9: Run tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=TesterProfileTest
```

Expected: all pass.

- [ ] **Step 10: Run full suite**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 306+ passing, 0 failures.

- [ ] **Step 11: Commit**

```
git add database/migrations/*create_tester_profiles* app/Models/TesterProfile.php app/Models/User.php app/Http/Controllers/Tester/ProfileController.php resources/views/tester/profile.blade.php tests/Feature/TesterProfileTest.php
git commit -m "feat(profile): add TesterProfile table, model, and extend tester profile page"
```

---

### Task 8: ManagerProfile Table + Portal Profile Page

**Files:**
- Create: migration `create_manager_profiles_table`
- Create: `app/Models/ManagerProfile.php`
- Modify: `app/Models/User.php`
- Create: `app/Http/Controllers/Manager/ProfileController.php`
- Create: `resources/views/manager/profile.blade.php`
- Modify: `resources/views/components/layouts/manager.blade.php` (add nav link)
- Modify: `routes/web.php` (add profile routes in manager group)
- Create: `tests/Feature/ManagerProfileTest.php`

---

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/ManagerProfileTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeManager(): User
    {
        $user = User::factory()->create();
        $user->assignRole('manager');
        return $user;
    }

    public function test_manager_profile_page_loads(): void
    {
        $manager = $this->makeManager();
        $response = $this->actingAs($manager)->get("/en/manager/profile");
        $response->assertStatus(200);
        $response->assertSee($manager->name);
    }

    public function test_manager_profile_update_saves_user_fields(): void
    {
        $manager = $this->makeManager();
        $response = $this->actingAs($manager)->patch("/en/manager/profile", [
            'name'  => 'Updated Manager',
            'phone' => '+237600000001',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $manager->id, 'name' => 'Updated Manager']);
    }

    public function test_manager_profile_update_saves_manager_profile_fields(): void
    {
        $manager = $this->makeManager();
        $response = $this->actingAs($manager)->patch("/en/manager/profile", [
            'name'             => $manager->name,
            'management_level' => 'team_lead',
            'bio'              => 'I manage teams.',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('manager_profiles', [
            'user_id'          => $manager->id,
            'management_level' => 'team_lead',
        ]);
    }

    public function test_non_manager_cannot_access_manager_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $response = $this->actingAs($user)->get("/en/manager/profile");
        $response->assertStatus(403);
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=ManagerProfileTest
```

Expected: failures.

- [ ] **Step 3: Create migration**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan make:migration create_manager_profiles_table
```

Edit the file:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('management_level')->nullable(); // team_lead, senior_manager, director
            $table->unsignedBigInteger('department_id')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_profiles');
    }
};
```

- [ ] **Step 4: Run migration**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan migrate
```

- [ ] **Step 5: Create `app/Models/ManagerProfile.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerProfile extends Model
{
    protected $fillable = ['user_id', 'management_level', 'department_id', 'bio'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function levelOptions(): array
    {
        return ['team_lead' => 'Team Lead', 'senior_manager' => 'Senior Manager', 'director' => 'Director'];
    }
}
```

- [ ] **Step 6: Add User relationship**

In `app/Models/User.php`:

```php
public function managerProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(\App\Models\ManagerProfile::class);
}
```

- [ ] **Step 7: Create `app/Http/Controllers/Manager/ProfileController.php`**

```php
<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ManagerProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('managerProfile');
        return view('manager.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'phone'            => 'nullable|string|max:30',
            'management_level' => 'nullable|string|in:team_lead,senior_manager,director',
            'bio'              => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);

        ManagerProfile::updateOrCreate(['user_id' => $user->id], [
            'management_level' => $validated['management_level'] ?? null,
            'bio'              => $validated['bio'] ?? null,
        ]);

        return redirect()
            ->route('manager.profile', ['locale' => app()->getLocale()])
            ->with('success', 'Profile updated successfully.');
    }
}
```

- [ ] **Step 8: Create `resources/views/manager/profile.blade.php`**

```blade
<x-layouts.manager title="My Profile">

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">My Profile</h1>
        <p class="cp-page-subtitle">Update your account details</p>
    </div>
</div>

@if(session('success'))
<div class="cp-flash-success mb-4">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 flex flex-col items-center text-center">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 text-2xl font-bold text-white"
             style="background:linear-gradient(135deg,#1A6FE8,#0056c4)">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <p class="text-lg font-semibold text-white">{{ $user->name }}</p>
        <p class="text-sm text-slate-400 mb-1">{{ $user->email }}</p>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-900/40 text-blue-300 border border-blue-800 mt-2">Manager</span>
        <p class="text-xs text-slate-500 mt-4">Member since {{ $user->created_at->format('M Y') }}</p>
    </div>

    <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h2 class="text-sm font-semibold text-white mb-5">Account Information</h2>
        <form method="POST" action="{{ route('manager.profile.update', ['locale' => app()->getLocale()]) }}">
            @csrf @method('PATCH')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Full Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500"
                           required maxlength="100">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500"
                           maxlength="30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Management Level</label>
                    <select name="management_level" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                        <option value="">— Select —</option>
                        @foreach(\App\Models\ManagerProfile::levelOptions() as $val => $label)
                            <option value="{{ $val }}" @selected(old('management_level', $user->managerProfile?->management_level) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Email (read-only)</label>
                    <input type="text" value="{{ $user->email }}" disabled
                           class="w-full bg-slate-800/50 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-slate-500 cursor-not-allowed">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Bio</label>
                    <textarea name="bio" rows="3" maxlength="2000"
                              class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">{{ old('bio', $user->managerProfile?->bio) }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white"
                        style="background:linear-gradient(135deg,#1A6FE8,#1258c4)">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
</x-layouts.manager>
```

- [ ] **Step 9: Add nav link to manager layout**

In `resources/views/components/layouts/manager.blade.php`, after the last `<a>` nav link (the Reports link), add:

```blade
                <a href="{{ route('manager.profile', ['locale' => $locale]) }}"
                   class="cp-nav-link {{ request()->routeIs('manager.profile') ? 'cp-nav-link-active-blue' : '' }}">
                    <i data-lucide="user" style="width:15px;height:15px"></i> My Profile
                </a>
```

- [ ] **Step 10: Add routes**

In `routes/web.php`, inside the manager route group (the block with `prefix('manager')` and `name('manager.')`), add after the last existing manager route:

```php
Route::get('/profile',  [\App\Http\Controllers\Manager\ProfileController::class, 'show'])->name('profile');
Route::patch('/profile', [\App\Http\Controllers\Manager\ProfileController::class, 'update'])->name('profile.update');
```

- [ ] **Step 11: Run tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=ManagerProfileTest
```

Expected: all pass.

- [ ] **Step 12: Run full suite**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 310+ passing, 0 failures.

- [ ] **Step 13: Commit**

```
git add database/migrations/*create_manager_profiles* app/Models/ManagerProfile.php app/Models/User.php app/Http/Controllers/Manager/ProfileController.php resources/views/manager/profile.blade.php resources/views/components/layouts/manager.blade.php routes/web.php tests/Feature/ManagerProfileTest.php
git commit -m "feat(profile): add ManagerProfile table and manager portal profile page"
```

---

### Task 9: SupportProfile Table + Portal Profile Page

**Files:**
- Create: migration `create_support_profiles_table`
- Create: `app/Models/SupportProfile.php`
- Modify: `app/Models/User.php`
- Create: `app/Http/Controllers/Support/ProfileController.php`
- Create: `resources/views/support/profile.blade.php`
- Modify: `resources/views/components/layouts/support.blade.php` (add nav link)
- Modify: `routes/web.php` (add profile routes in support group)
- Create: `tests/Feature/SupportProfileTest.php`

**Note:** The support layout uses the same `cp-nav-link-active-*` pattern. Check which active color is used in `resources/views/components/layouts/support.blade.php` before writing the view/layout changes.

---

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/SupportProfileTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeSupport(): User
    {
        $user = User::factory()->create();
        $user->assignRole('support');
        return $user;
    }

    public function test_support_profile_page_loads(): void
    {
        $support = $this->makeSupport();
        $response = $this->actingAs($support)->get("/en/support/profile");
        $response->assertStatus(200);
        $response->assertSee($support->name);
    }

    public function test_support_profile_update_saves_fields(): void
    {
        $support = $this->makeSupport();
        $response = $this->actingAs($support)->patch("/en/support/profile", [
            'name'                    => $support->name,
            'ticket_specialization'   => 'technical',
            'shift'                   => 'morning',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('support_profiles', [
            'user_id'               => $support->id,
            'ticket_specialization' => 'technical',
            'shift'                 => 'morning',
        ]);
    }

    public function test_non_support_cannot_access_support_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $response = $this->actingAs($user)->get("/en/support/profile");
        $response->assertStatus(403);
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=SupportProfileTest
```

Expected: failures.

- [ ] **Step 3: Create migration**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan make:migration create_support_profiles_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('ticket_specialization')->default('all'); // technical, billing, general, all
            $table->string('shift')->nullable(); // morning, afternoon, evening
            $table->text('languages')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_profiles');
    }
};
```

- [ ] **Step 4: Run migration**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan migrate
```

- [ ] **Step 5: Create `app/Models/SupportProfile.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportProfile extends Model
{
    protected $fillable = ['user_id', 'ticket_specialization', 'shift', 'languages', 'bio'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function specializationOptions(): array
    {
        return ['all' => 'All', 'technical' => 'Technical', 'billing' => 'Billing', 'general' => 'General'];
    }

    public static function shiftOptions(): array
    {
        return ['morning' => 'Morning', 'afternoon' => 'Afternoon', 'evening' => 'Evening'];
    }
}
```

- [ ] **Step 6: Add User relationship**

In `app/Models/User.php`:

```php
public function supportProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(\App\Models\SupportProfile::class);
}
```

- [ ] **Step 7: Create `app/Http/Controllers/Support/ProfileController.php`**

```php
<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('supportProfile');
        return view('support.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:100',
            'phone'                  => 'nullable|string|max:30',
            'ticket_specialization'  => 'nullable|string|in:all,technical,billing,general',
            'shift'                  => 'nullable|string|in:morning,afternoon,evening',
            'languages'              => 'nullable|string|max:500',
            'bio'                    => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);

        SupportProfile::updateOrCreate(['user_id' => $user->id], [
            'ticket_specialization' => $validated['ticket_specialization'] ?? 'all',
            'shift'                 => $validated['shift'] ?? null,
            'languages'             => $validated['languages'] ?? null,
            'bio'                   => $validated['bio'] ?? null,
        ]);

        return redirect()
            ->route('support.profile', ['locale' => app()->getLocale()])
            ->with('success', 'Profile updated successfully.');
    }
}
```

- [ ] **Step 8: Read the support layout's active color class**

Open `resources/views/components/layouts/support.blade.php` and look for the active nav link class used (e.g., `cp-nav-link-active-teal` or `cp-nav-link-active-blue`). Use that class in both the nav link addition and the profile view.

- [ ] **Step 9: Create `resources/views/support/profile.blade.php`**

```blade
<x-layouts.support title="My Profile">

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">My Profile</h1>
        <p class="cp-page-subtitle">Update your account details</p>
    </div>
</div>

@if(session('success'))
<div class="cp-flash-success mb-4">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 flex flex-col items-center text-center">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 text-2xl font-bold text-white"
             style="background:linear-gradient(135deg,#0694a2,#047481)">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <p class="text-lg font-semibold text-white">{{ $user->name }}</p>
        <p class="text-sm text-slate-400 mb-1">{{ $user->email }}</p>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-teal-900/40 text-teal-300 border border-teal-800 mt-2">Support</span>
        <p class="text-xs text-slate-500 mt-4">Member since {{ $user->created_at->format('M Y') }}</p>
    </div>

    <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h2 class="text-sm font-semibold text-white mb-5">Account Information</h2>
        <form method="POST" action="{{ route('support.profile.update', ['locale' => app()->getLocale()]) }}">
            @csrf @method('PATCH')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Full Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-teal-500"
                           required maxlength="100">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-teal-500"
                           maxlength="30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Ticket Specialization</label>
                    <select name="ticket_specialization" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-teal-500">
                        @foreach(\App\Models\SupportProfile::specializationOptions() as $val => $label)
                            <option value="{{ $val }}" @selected(old('ticket_specialization', $user->supportProfile?->ticket_specialization ?? 'all') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Shift</label>
                    <select name="shift" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-teal-500">
                        <option value="">— Select —</option>
                        @foreach(\App\Models\SupportProfile::shiftOptions() as $val => $label)
                            <option value="{{ $val }}" @selected(old('shift', $user->supportProfile?->shift) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Languages (comma-separated)</label>
                    <input type="text" name="languages" value="{{ old('languages', $user->supportProfile?->languages) }}"
                           class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-teal-500"
                           maxlength="500" placeholder="English, French">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Bio</label>
                    <textarea name="bio" rows="3" maxlength="2000"
                              class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-teal-500">{{ old('bio', $user->supportProfile?->bio) }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white"
                        style="background:linear-gradient(135deg,#0694a2,#047481)">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
</x-layouts.support>
```

- [ ] **Step 10: Add nav link to support layout**

In `resources/views/components/layouts/support.blade.php`, add after the last nav link:

```blade
                <a href="{{ route('support.profile', ['locale' => $locale]) }}"
                   class="cp-nav-link {{ request()->routeIs('support.profile') ? 'cp-nav-link-active-teal' : '' }}">
                    <i data-lucide="user" style="width:15px;height:15px"></i> My Profile
                </a>
```

(Replace `cp-nav-link-active-teal` with whatever active class the layout uses if different.)

- [ ] **Step 11: Add routes**

In `routes/web.php`, inside the support route group, add:

```php
Route::get('/profile',   [\App\Http\Controllers\Support\ProfileController::class, 'show'])->name('profile');
Route::patch('/profile', [\App\Http\Controllers\Support\ProfileController::class, 'update'])->name('profile.update');
```

- [ ] **Step 12: Run tests and full suite**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=SupportProfileTest
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: all pass.

- [ ] **Step 13: Commit**

```
git add database/migrations/*create_support_profiles* app/Models/SupportProfile.php app/Models/User.php app/Http/Controllers/Support/ProfileController.php resources/views/support/profile.blade.php resources/views/components/layouts/support.blade.php routes/web.php tests/Feature/SupportProfileTest.php
git commit -m "feat(profile): add SupportProfile table and support portal profile page"
```

---

### Task 10: AccountantProfile Table + Portal Profile Page

**Files:**
- Create: migration `create_accountant_profiles_table`
- Create: `app/Models/AccountantProfile.php`
- Modify: `app/Models/User.php`
- Create: `app/Http/Controllers/Accountant/ProfileController.php`
- Create: `resources/views/accountant/profile.blade.php`
- Modify: `resources/views/components/layouts/accountant.blade.php` (add nav link)
- Modify: `routes/web.php`
- Create: `tests/Feature/AccountantProfileTest.php`

---

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/AccountantProfileTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountantProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeAccountant(): User
    {
        $user = User::factory()->create();
        $user->assignRole('accountant');
        return $user;
    }

    public function test_accountant_profile_page_loads(): void
    {
        $accountant = $this->makeAccountant();
        $response = $this->actingAs($accountant)->get("/en/accountant/profile");
        $response->assertStatus(200);
        $response->assertSee($accountant->name);
    }

    public function test_accountant_profile_update_saves_fields(): void
    {
        $accountant = $this->makeAccountant();
        $response = $this->actingAs($accountant)->patch("/en/accountant/profile", [
            'name'                       => $accountant->name,
            'accounting_specialization'  => 'payroll',
            'bio'                        => 'I handle payroll.',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('accountant_profiles', [
            'user_id'                    => $accountant->id,
            'accounting_specialization'  => 'payroll',
        ]);
    }

    public function test_non_accountant_cannot_access_accountant_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $response = $this->actingAs($user)->get("/en/accountant/profile");
        $response->assertStatus(403);
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=AccountantProfileTest
```

- [ ] **Step 3: Create migration**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan make:migration create_accountant_profiles_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accountant_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('accounting_specialization')->default('general'); // tax, payroll, audit, general
            $table->text('certifications')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accountant_profiles');
    }
};
```

- [ ] **Step 4: Run migration**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan migrate
```

- [ ] **Step 5: Create `app/Models/AccountantProfile.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountantProfile extends Model
{
    protected $fillable = ['user_id', 'accounting_specialization', 'certifications', 'bio'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function specializationOptions(): array
    {
        return ['general' => 'General', 'tax' => 'Tax', 'payroll' => 'Payroll', 'audit' => 'Audit'];
    }
}
```

- [ ] **Step 6: Add User relationship**

```php
public function accountantProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(\App\Models\AccountantProfile::class);
}
```

- [ ] **Step 7: Create `app/Http/Controllers/Accountant/ProfileController.php`**

```php
<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\AccountantProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('accountantProfile');
        return view('accountant.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'                      => 'required|string|max:100',
            'phone'                     => 'nullable|string|max:30',
            'accounting_specialization' => 'nullable|string|in:general,tax,payroll,audit',
            'certifications'            => 'nullable|string|max:1000',
            'bio'                       => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);

        AccountantProfile::updateOrCreate(['user_id' => $user->id], [
            'accounting_specialization' => $validated['accounting_specialization'] ?? 'general',
            'certifications'            => $validated['certifications'] ?? null,
            'bio'                       => $validated['bio'] ?? null,
        ]);

        return redirect()
            ->route('accountant.profile', ['locale' => app()->getLocale()])
            ->with('success', 'Profile updated successfully.');
    }
}
```

- [ ] **Step 8: Create `resources/views/accountant/profile.blade.php`**

```blade
<x-layouts.accountant title="My Profile">

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">My Profile</h1>
        <p class="cp-page-subtitle">Update your account details</p>
    </div>
</div>

@if(session('success'))
<div class="cp-flash-success mb-4">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 flex flex-col items-center text-center">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 text-2xl font-bold text-white"
             style="background:linear-gradient(135deg,#d97706,#92400e)">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <p class="text-lg font-semibold text-white">{{ $user->name }}</p>
        <p class="text-sm text-slate-400 mb-1">{{ $user->email }}</p>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-900/40 text-amber-300 border border-amber-800 mt-2">Accountant</span>
        <p class="text-xs text-slate-500 mt-4">Member since {{ $user->created_at->format('M Y') }}</p>
    </div>

    <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h2 class="text-sm font-semibold text-white mb-5">Account Information</h2>
        <form method="POST" action="{{ route('accountant.profile.update', ['locale' => app()->getLocale()]) }}">
            @csrf @method('PATCH')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Full Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500"
                           required maxlength="100">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500"
                           maxlength="30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Specialization</label>
                    <select name="accounting_specialization" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500">
                        @foreach(\App\Models\AccountantProfile::specializationOptions() as $val => $label)
                            <option value="{{ $val }}" @selected(old('accounting_specialization', $user->accountantProfile?->accounting_specialization ?? 'general') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Certifications</label>
                    <textarea name="certifications" rows="2" maxlength="1000"
                              class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500">{{ old('certifications', $user->accountantProfile?->certifications) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Bio</label>
                    <textarea name="bio" rows="3" maxlength="2000"
                              class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500">{{ old('bio', $user->accountantProfile?->bio) }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white"
                        style="background:linear-gradient(135deg,#d97706,#92400e)">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
</x-layouts.accountant>
```

- [ ] **Step 9: Add nav link to accountant layout**

In `resources/views/components/layouts/accountant.blade.php`, after the last nav link:

```blade
                <a href="{{ route('accountant.profile', ['locale' => $locale]) }}"
                   class="cp-nav-link {{ request()->routeIs('accountant.profile') ? 'cp-nav-link-active-amber' : '' }}">
                    <i data-lucide="user" style="width:15px;height:15px"></i> My Profile
                </a>
```

- [ ] **Step 10: Add routes**

In `routes/web.php`, inside the accountant route group:

```php
Route::get('/profile',   [\App\Http\Controllers\Accountant\ProfileController::class, 'show'])->name('profile');
Route::patch('/profile', [\App\Http\Controllers\Accountant\ProfileController::class, 'update'])->name('profile.update');
```

- [ ] **Step 11: Run tests and full suite**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=AccountantProfileTest
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

- [ ] **Step 12: Commit**

```
git add database/migrations/*create_accountant_profiles* app/Models/AccountantProfile.php app/Models/User.php app/Http/Controllers/Accountant/ProfileController.php resources/views/accountant/profile.blade.php resources/views/components/layouts/accountant.blade.php routes/web.php tests/Feature/AccountantProfileTest.php
git commit -m "feat(profile): add AccountantProfile table and accountant portal profile page"
```

---

### Task 11: HR Portal Profile Page (EmployeeProfile — No New Table)

**Files:**
- Create: `app/Http/Controllers/HR/ProfileController.php`
- Create: `resources/views/hr/profile.blade.php`
- Modify: `resources/views/components/layouts/hr.blade.php` (add nav link)
- Modify: `routes/web.php` (add profile routes in hr group)
- Create: `tests/Feature/HrProfileTest.php`

**Note:** HR users are employees. Check `app/Models/User.php` for an `employeeProfile()` relationship. Also check `app/Models/EmployeeProfile.php` for its `$fillable` fields. The form should edit both `User` fields and `EmployeeProfile` fields that are appropriate for self-editing (not HR-admin fields like salary).

---

- [ ] **Step 1: Inspect EmployeeProfile fields**

Read `app/Models/EmployeeProfile.php` to see `$fillable`. Only expose fields appropriate for self-editing: department display, job title, bio — NOT salary, hire date, or admin-only fields.

- [ ] **Step 2: Write the failing tests**

Create `tests/Feature/HrProfileTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeHr(): User
    {
        $user = User::factory()->create();
        $user->assignRole('hr');
        return $user;
    }

    public function test_hr_profile_page_loads(): void
    {
        $hr = $this->makeHr();
        $response = $this->actingAs($hr)->get("/en/hr/profile");
        $response->assertStatus(200);
        $response->assertSee($hr->name);
    }

    public function test_hr_profile_update_saves_user_fields(): void
    {
        $hr = $this->makeHr();
        $response = $this->actingAs($hr)->patch("/en/hr/profile", [
            'name'  => 'Updated HR',
            'phone' => '+237600000002',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $hr->id, 'name' => 'Updated HR']);
    }

    public function test_non_hr_cannot_access_hr_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $response = $this->actingAs($user)->get("/en/hr/profile");
        $response->assertStatus(403);
    }
}
```

- [ ] **Step 3: Run tests to confirm they fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=HrProfileTest
```

- [ ] **Step 4: Create `app/Http/Controllers/HR/ProfileController.php`**

```php
<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('employeeProfile');
        return view('hr.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'bio'   => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);

        if ($user->employeeProfile && isset($validated['bio'])) {
            $user->employeeProfile->update(['bio' => $validated['bio']]);
        }

        return redirect()
            ->route('hr.profile', ['locale' => app()->getLocale()])
            ->with('success', 'Profile updated successfully.');
    }
}
```

**Note:** If `EmployeeProfile` has no `bio` column, remove the `bio` field from the form/validation. Check `app/Models/EmployeeProfile.php` first.

- [ ] **Step 5: Create `resources/views/hr/profile.blade.php`**

```blade
<x-layouts.hr title="My Profile">

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">My Profile</h1>
        <p class="cp-page-subtitle">Update your account details</p>
    </div>
</div>

@if(session('success'))
<div class="cp-flash-success mb-4">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 flex flex-col items-center text-center">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 text-2xl font-bold text-white"
             style="background:linear-gradient(135deg,#7c3aed,#5b21b6)">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <p class="text-lg font-semibold text-white">{{ $user->name }}</p>
        <p class="text-sm text-slate-400 mb-1">{{ $user->email }}</p>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-purple-900/40 text-purple-300 border border-purple-800 mt-2">HR</span>
        @if($user->employeeProfile?->job_title)
            <p class="text-xs text-slate-400 mt-2">{{ $user->employeeProfile->job_title }}</p>
        @endif
        <p class="text-xs text-slate-500 mt-4">Member since {{ $user->created_at->format('M Y') }}</p>
    </div>

    <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h2 class="text-sm font-semibold text-white mb-5">Account Information</h2>
        <form method="POST" action="{{ route('hr.profile.update', ['locale' => app()->getLocale()]) }}">
            @csrf @method('PATCH')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Full Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500"
                           required maxlength="100">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500"
                           maxlength="30">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Email (read-only)</label>
                    <input type="text" value="{{ $user->email }}" disabled
                           class="w-full bg-slate-800/50 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-slate-500 cursor-not-allowed">
                </div>
                @if($user->employeeProfile)
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Job Title (read-only)</label>
                    <input type="text" value="{{ $user->employeeProfile->job_title ?? '—' }}" disabled
                           class="w-full bg-slate-800/50 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-slate-500 cursor-not-allowed">
                </div>
                @endif
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white"
                        style="background:linear-gradient(135deg,#7c3aed,#5b21b6)">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
</x-layouts.hr>
```

- [ ] **Step 6: Add nav link to HR layout**

In `resources/views/components/layouts/hr.blade.php`, after the last nav link:

```blade
                <a href="{{ route('hr.profile', ['locale' => $locale]) }}"
                   class="cp-nav-link {{ request()->routeIs('hr.profile') ? 'cp-nav-link-active-purple' : '' }}">
                    <i data-lucide="user" style="width:15px;height:15px"></i> My Profile
                </a>
```

- [ ] **Step 7: Add routes**

In `routes/web.php`, inside the hr route group:

```php
Route::get('/profile',   [\App\Http\Controllers\HR\ProfileController::class, 'show'])->name('profile');
Route::patch('/profile', [\App\Http\Controllers\HR\ProfileController::class, 'update'])->name('profile.update');
```

- [ ] **Step 8: Run tests and full suite**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=HrProfileTest
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 325+ passing, 0 failures.

- [ ] **Step 9: Commit**

```
git add app/Http/Controllers/HR/ProfileController.php resources/views/hr/profile.blade.php resources/views/components/layouts/hr.blade.php routes/web.php tests/Feature/HrProfileTest.php
git commit -m "feat(profile): add HR portal profile page using EmployeeProfile"
```

---

## Success Criteria

| Area | Metric |
|---|---|
| Filament RBAC | 0 resources missing `canAccess()` for support-sensitive data |
| Confidential routes | Support role cannot access `/*/strategy`, `/*/risk`, etc. |
| Policy coverage | 4 Policy classes registered, controllers use `authorize()` instead of `abort_if` |
| Survey validation | Invalid ratings/choices return 422 |
| Blog validation | Invalid category/overlong search return 422 |
| Profile completeness | All 5 non-admin portals have working profile pages |
| Test suite | 325+ tests, 0 failures |
