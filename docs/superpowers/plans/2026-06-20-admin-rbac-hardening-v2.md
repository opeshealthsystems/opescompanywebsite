# Admin / RBAC Hardening v2 — Fix Plan

> Fixes the 7 confirmed + 2 actioned-minor findings from the admin-panel audit (`docs/superpowers/audits/2026-06-20-admin-rbac-audit.json`). TDD per task; branch `fix/admin-rbac-hardening-v2`; PHP `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`. Baseline 433 green.

**Decisions:** support stays in Filament (login unchanged — finding #10 no-op); finance/lead aggregate counts hidden from support in `OpesDashboardStats` (#8).

---

## Task 1 (BLOCKER #1): UserResource role-assignment guard
**Files:** `app/Filament/Resources/UserResource.php` (+ Create/Edit pages as needed); Test `tests/Feature/UserResourceRbacTest.php`.
- The `roles` CheckboxList must not let a non-`super_admin` grant `super_admin`. Two layers:
  1. **UI:** `->options()` returns all roles for super_admin, else all roles except `super_admin`.
  2. **Server guard (defense-in-depth):** strip `super_admin` from the submitted roles unless `auth()->user()->hasRole('super_admin')` — in `mutateFormDataBeforeSave`/`BeforeCreate` on the pages, or by re-filtering after save. Because Filament syncs the `roles` relationship from form state, enforce by overriding the role sync: simplest is to filter the CheckboxList state via `->saveRelationshipsUsing()` or guard in the page. Implement whichever the codebase supports cleanly after reading the file.
- **Test (Livewire):** `Livewire::test(EditUser)` (or CreateUser) as an `admin`, fill `roles` with the super_admin role id, save, assert the target user does NOT have `super_admin`; repeat as `super_admin` and assert it DOES. Plus assert the available options for admin exclude super_admin.
- Commit: `fix(rbac): prevent non-super_admin from granting the super_admin role`.

## Task 2 (BLOCKER #2): Customer dashboard 500
**Files:** `app/Http/Controllers/Customer/DashboardController.php`; Test `tests/Feature/CustomerDashboardTest.php`.
- Line ~18: `License::where('customer_id', $user->id)` → `License::where('user_id', $user->id)`.
- **Test:** a `customer` user GET `/en/customer/dashboard` → `assertOk()` (currently 500). Seed an active license with `user_id` and assert the active-license stat renders.
- Commit: `fix(customer): use user_id (not customer_id) for license count on dashboard`.

## Task 3 (IMPORTANT #3,#4): remove support from sensitive resources
**Files:** `app/Filament/Resources/DocumentResource.php`, `app/Http/Controllers/DocumentController.php` (the `pdf` download), `app/Filament/Resources/LeaveRequestResource.php`; Test `tests/Feature/SupportResourceAccessTest.php`.
- `DocumentResource::canAccess()` → `hasAnyRole(['super_admin','admin'])` (drop `support`). `DocumentController::pdf` ownership/role check → drop `support`.
- `LeaveRequestResource::canAccess()` → `hasAnyRole(['super_admin','admin'])` (drop `support`).
- **Test:** acting as `support`, `DocumentResource::canAccess()` and `LeaveRequestResource::canAccess()` are `false`; as `admin`, `true`.
- Commit: `fix(rbac): restrict Documents and Leave Requests to admins (drop support)`.

## Task 4 (IMPORTANT #5,#6 + MINOR #8): gate dashboard widgets from support
**Files:** `app/Filament/Widgets/RecentInvoicesWidget.php`, `RecentLeadsWidget.php`, `OpesDashboardStats.php`; Test `tests/Feature/DashboardWidgetAccessTest.php`.
- `RecentInvoicesWidget`: add `public static function canView(): bool { return auth()->user()?->hasPermissionTo('manage_accounting') ?? false; }`.
- `RecentLeadsWidget`: add `canView()` → `hasAnyRole(['super_admin','admin'])`.
- `OpesDashboardStats::getStats()`: only include the finance (Outstanding Invoices/overdue) and lead (New Leads/qualified) stat cards when `auth()->user()?->hasAnyRole(['super_admin','admin'])` (or `hasPermissionTo('manage_accounting')` for finance); support keeps operational stats only.
- **Test:** acting as `support`, `RecentInvoicesWidget::canView()` and `RecentLeadsWidget::canView()` are `false`; as `admin`, `true`. For `OpesDashboardStats`, acting as support, assert the returned stats do not include the finance/lead labels; as admin they do.
- Commit: `fix(rbac): hide finance/lead dashboard widgets and stats from support`.

## Task 5 (IMPORTANT #7): HR employee-search PII leak
**Files:** `app/Http/Controllers/HR/EmployeeController.php`; Test `tests/Feature/HrEmployeeSearchTest.php`.
- Wrap the search OR in a closure so `whereNotNull('employee_id')` stays an outer AND:
  ```php
  ->when($request->search, fn ($q) => $q->where(fn ($w) =>
      $w->where('name', 'like', '%'.$request->search.'%')
        ->orWhere('email', 'like', '%'.$request->search.'%')))
  ```
- **Test:** seed an employee (`employee_id` set) and a customer (no `employee_id`) whose email matches the search term; as `hr`, GET `/en/hr/employees?search=<term>` → sees the employee, does NOT see the customer.
- Commit: `fix(hr): group employee-search OR so non-employees cannot leak into the directory`.

## Task 6 (MINOR #9,#11): cleanups
**Files:** delete `resources/views/auth/practitioner-register.blade.php`; `app/Http/Controllers/Practitioner/DashboardController.php`; Test: extend an existing practitioner dashboard test or add a render assertion.
- Delete the orphaned `practitioner-register.blade.php` (references the unregistered `practitioner.register.post`; the flow is now unified `auth.register`).
- Practitioner dashboard payout query (lines ~33-40): group the OR + drop the redundant duplicate clause:
  ```php
  PractitionerApplication::where('practitioner_id', $user->id)
      ->where(fn ($q) => $q->where('payout_status', '!=', 'not_applicable')->orWhere('status', 'approved'))
      ->with('program')->latest()->take(5)->get();
  ```
- **Test:** `practitioner` GET `/en/practitioner/dashboard` → `assertOk()`.
- Commit: `chore(cleanup): remove orphaned practitioner-register view; tidy practitioner payout query`.

---

## Final verification
- Full suite green (`<php> artisan test`), expect ~445 (433 + ~12 new).
- Manual: customer dashboard loads; an admin cannot grant super_admin; support sees no Documents/Leave resources, no invoice/lead widgets or finance counts; HR search returns only employees.

## No-op (documented)
- #10 support→Filament: intentional, login unchanged. The standalone support web portal remains reachable by direct URL but is not the login target.
