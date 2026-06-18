# Portal Enhancements + Practitioner Directory — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Rebuild the bare Tester portal into a full-featured dashboard, create a dedicated Support portal (currently support agents land in raw Filament admin), and build a public Practitioner Directory showcasing contributor portfolios.

**Architecture:** Three independent sub-systems. Tester and Support portals follow the existing portal pattern — a `<x-layouts.X>` Blade component wrapping controller-driven views, gated by `role:X` middleware inside the `{locale}` route group. The Practitioner Directory is a new set of public routes under `{locale}` using the existing `<x-layouts.app>` public layout — no auth required, only non-sensitive fields exposed. All dark-theme `cp-*` / `portal-*` CSS classes are already compiled in `public/build`; no Vite rebuild needed unless new classes are added.

**Tech Stack:** Laravel 13.8, PHP 8.3, Blade, Spatie Permission v8, Lucide icons, custom `cp-*` CSS (compiled). Tests: PHPUnit via `php artisan test`. PHP binary: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`.

---

## Key Model Reference (read before touching any file)

| Model | Table | Relevant fields |
|-------|-------|-----------------|
| `TesterAssignment` | `tester_assignments` | `assigned_to`, `assigned_by`, `product_name`, `title`, `status` (`pending\|in_progress\|completed\|cancelled`), `due_date` |
| `Ticket` | `tickets` | `user_id`, `assigned_to`, `subject`, `description`, `type`, `status`, `priority`, `tester_assignment_id`, `sla_response_due_at`, `sla_resolution_due_at`, `resolved_at` |
| `TicketReply` | `ticket_replies` | `ticket_id`, `user_id`, `body`, `is_internal`; relationships: `ticket()`, `author()` |
| `Ticket` relationships | — | `customer()→User`, `assignee()→User`, `replies()→HasMany(TicketReply)`, `publicReplies()→HasMany(TicketReply)` |
| `PractitionerProfile` | `practitioner_profiles` | `user_id`, `profession`, `specialty`, `workplace_name`, `workplace_city`, `workplace_country`, `years_of_experience`, `bio`, `opes_testimonial`, `is_verified` |
| `PractitionerApplication` | `practitioner_applications` | `practitioner_id`, `program_id`, `status` (`pending\|approved\|rejected\|withdrawn`), `payout_status` |
| `PractitionerFinding` | `practitioner_findings` | `application_id`, `practitioner_id`, `is_published`, `overall_rating`, `wait_time_rating`, `data_integrity_rating`, `usability_rating`, `findings_text` |
| `PractitionerProgram` | `practitioner_programs` | `product_name`, `title`, `type` (`volunteer\|paid`), `compensation`, `status`, `starts_at`, `ends_at` |
| `User` | `users` | `name`, `email`, `phone`, `avatar`; `practitionerProfile()→HasOne(PractitionerProfile)`, `practitionerTier()` method |

---

## File Structure Map

### Tester Portal (enhanced)
```
Modify:  app/Http/Controllers/Tester/DashboardController.php
Create:  app/Http/Controllers/Tester/ProfileController.php
Create:  app/Http/Controllers/Tester/BugReportController.php
Modify:  resources/views/tester/dashboard.blade.php
Create:  resources/views/tester/profile.blade.php
Create:  resources/views/tester/bug-reports/index.blade.php
Modify:  resources/views/components/layouts/tester.blade.php
Modify:  routes/web.php  (add 3 routes inside existing tester group)
Create:  tests/Feature/TesterPortalEnhancedTest.php
```

### Support Portal (new)
```
Create:  app/Http/Controllers/Support/DashboardController.php
Create:  app/Http/Controllers/Support/TicketController.php
Create:  resources/views/components/layouts/support.blade.php
Create:  resources/views/support/dashboard.blade.php
Create:  resources/views/support/tickets/index.blade.php
Create:  resources/views/support/tickets/show.blade.php
Modify:  routes/web.php  (add new support group before closing locale group)
Create:  tests/Feature/SupportPortalTest.php
```

### Practitioner Directory (public)
```
Create:  app/Http/Controllers/Public/PractitionerDirectoryController.php
Create:  resources/views/pages/practitioners/index.blade.php
Create:  resources/views/pages/practitioners/show.blade.php
Modify:  routes/web.php  (add 2 public routes inside locale group)
Create:  tests/Feature/PractitionerDirectoryTest.php
```

---

## Task 1: Enhanced Tester Dashboard

**Files:**
- Modify: `app/Http/Controllers/Tester/DashboardController.php`
- Modify: `resources/views/tester/dashboard.blade.php`
- Create: `tests/Feature/TesterPortalEnhancedTest.php`

- [ ] **Step 1.1 — Write the failing test**

```php
// tests/Feature/TesterPortalEnhancedTest.php
<?php
namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TesterAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesterPortalEnhancedTest extends TestCase
{
    use RefreshDatabase;

    private function testerUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('tester');
        return $user;
    }

    public function test_dashboard_shows_kpi_counts(): void
    {
        $user = $this->testerUser();

        TesterAssignment::create([
            'assigned_to'  => $user->id,
            'assigned_by'  => null,
            'product_slug' => 'opes-clinic',
            'product_name' => 'OPES Clinic',
            'title'        => 'Test login flow',
            'description'  => 'Verify login works',
            'status'       => 'completed',
        ]);
        TesterAssignment::create([
            'assigned_to'  => $user->id,
            'assigned_by'  => null,
            'product_slug' => 'opes-hospital',
            'product_name' => 'OPES Hospital',
            'title'        => 'Test dashboard',
            'description'  => 'Verify dashboard loads',
            'status'       => 'in_progress',
        ]);
        Ticket::create([
            'user_id'     => $user->id,
            'subject'     => 'Bug in login',
            'description' => 'Login fails',
            'type'        => 'bug_report',
            'status'      => 'open',
            'priority'    => 'medium',
        ]);

        $this->actingAs($user)
            ->get(route('tester.dashboard', ['locale' => 'en']))
            ->assertOk()
            ->assertViewHas('totalAssigned', 2)
            ->assertViewHas('completedCount', 1)
            ->assertViewHas('activeCount', 1)
            ->assertViewHas('bugReportsCount', 1);
    }

    public function test_dashboard_is_blocked_for_non_tester(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)
            ->get(route('tester.dashboard', ['locale' => 'en']))
            ->assertForbidden();
    }
}
```

- [ ] **Step 1.2 — Run test to confirm it fails**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=TesterPortalEnhancedTest
```
Expected: FAIL — `assertViewHas('totalAssigned')` fails because controller doesn't pass those variables.

- [ ] **Step 1.3 — Update the DashboardController**

```php
// app/Http/Controllers/Tester/DashboardController.php
<?php
namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TesterAssignment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $allAssignments = TesterAssignment::where('assigned_to', $user->id)->get();

        $active    = $allAssignments->whereIn('status', ['pending', 'in_progress'])->sortBy('due_date');
        $completed = $allAssignments->whereIn('status', ['completed', 'cancelled'])
                                    ->sortByDesc('updated_at')->take(5);
        $overdue   = $allAssignments->filter(fn ($a) => $a->isOverdue());

        $totalAssigned   = $allAssignments->count();
        $activeCount     = $active->count();
        $completedCount  = $allAssignments->where('status', 'completed')->count();
        $overdueCount    = $overdue->count();
        $bugReportsCount = Ticket::where('user_id', $user->id)
                                 ->where('type', 'bug_report')
                                 ->count();

        return view('tester.dashboard', compact(
            'user', 'active', 'completed',
            'totalAssigned', 'activeCount', 'completedCount', 'overdueCount', 'bugReportsCount'
        ));
    }
}
```

- [ ] **Step 1.4 — Rebuild the dashboard view with KPI cards**

```blade
{{-- resources/views/tester/dashboard.blade.php --}}
<x-layouts.tester title="Dashboard">
@php $locale = app()->getLocale(); @endphp

<div class="flex items-start justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-bold text-white mb-0.5">Welcome back, {{ $user->name }}</h1>
        <p class="text-slate-400 text-sm">OPES Tester Dashboard</p>
    </div>
</div>

{{-- KPI row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    @php
    $kpis = [
        ['label'=>'Total Assigned',   'value'=>$totalAssigned,   'icon'=>'clipboard-list',  'color'=>'#1A6FE8'],
        ['label'=>'Active',           'value'=>$activeCount,     'icon'=>'activity',        'color'=>'#F59E0B'],
        ['label'=>'Completed',        'value'=>$completedCount,  'icon'=>'check-circle',    'color'=>'#00C896'],
        ['label'=>'Bug Reports Filed','value'=>$bugReportsCount, 'icon'=>'bug',             'color'=>'#ef4444'],
    ];
    @endphp
    @foreach($kpis as $kpi)
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0"
             style="background:{{ $kpi['color'] }}1a">
            <i data-lucide="{{ $kpi['icon'] }}" style="width:22px;height:22px;color:{{ $kpi['color'] }}"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-white">{{ $kpi['value'] }}</p>
            <p class="text-xs text-slate-400">{{ $kpi['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

@if($overdueCount > 0)
<div class="bg-red-900/20 border border-red-800 rounded-lg px-5 py-4 mb-6 flex items-center gap-3">
    <i data-lucide="alert-triangle" style="width:18px;height:18px;color:#ef4444;flex-shrink:0"></i>
    <p class="text-red-300 text-sm font-medium">You have {{ $overdueCount }} overdue assignment{{ $overdueCount > 1 ? 's' : '' }}. Please update their status.</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Active assignments --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h2 class="font-semibold text-white text-sm mb-4 flex items-center gap-2">
            <i data-lucide="activity" style="width:16px;height:16px;color:#F59E0B"></i> Active Assignments
        </h2>
        @forelse($active as $a)
        @php $overdue = $a->isOverdue(); @endphp
        <div class="flex items-start justify-between gap-3 mb-3 pb-3 border-b border-slate-800 last:border-0 last:mb-0 last:pb-0">
            <div>
                <p class="text-sm text-slate-200 font-medium">{{ $a->title }}</p>
                <p class="text-xs text-slate-500">{{ $a->product_name }}</p>
                @if($a->due_date)
                <p class="text-xs mt-0.5 {{ $overdue ? 'text-red-400 font-semibold' : 'text-slate-500' }}">
                    Due {{ $a->due_date->format('d M Y') }}{{ $overdue ? ' — OVERDUE' : '' }}
                </p>
                @endif
            </div>
            <a href="{{ route('tester.assignments.show', ['locale'=>$locale,'id'=>$a->id]) }}"
               class="text-xs text-emerald-400 hover:underline no-underline flex-shrink-0">View →</a>
        </div>
        @empty
        <p class="text-slate-500 text-sm text-center py-4">No active assignments. Check back soon.</p>
        @endforelse
    </div>

    {{-- Recently completed --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h2 class="font-semibold text-white text-sm mb-4 flex items-center gap-2">
            <i data-lucide="check-circle" style="width:16px;height:16px;color:#00C896"></i> Recently Completed
        </h2>
        @forelse($completed as $a)
        <div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-800 last:border-0 last:mb-0 last:pb-0">
            <div>
                <p class="text-sm text-slate-300">{{ $a->title }}</p>
                <p class="text-xs text-slate-500">{{ $a->product_name }}</p>
            </div>
            <span class="text-xs text-emerald-400 font-semibold">Completed</span>
        </div>
        @empty
        <p class="text-slate-500 text-sm text-center py-4">No completed assignments yet.</p>
        @endforelse
    </div>
</div>
</x-layouts.tester>
```

- [ ] **Step 1.5 — Run the tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=TesterPortalEnhancedTest
```
Expected: All tests PASS.

- [ ] **Step 1.6 — Commit**

```
git add app/Http/Controllers/Tester/DashboardController.php resources/views/tester/dashboard.blade.php tests/Feature/TesterPortalEnhancedTest.php
git commit -m "feat(tester): rebuild dashboard with KPI stats"
```

---

## Task 2: Tester Profile Page

**Files:**
- Create: `app/Http/Controllers/Tester/ProfileController.php`
- Create: `resources/views/tester/profile.blade.php`

- [ ] **Step 2.1 — Add profile tests to existing test file**

Append to `tests/Feature/TesterPortalEnhancedTest.php`:

```php
public function test_tester_can_view_profile(): void
{
    $user = $this->testerUser();

    $this->actingAs($user)
        ->get(route('tester.profile', ['locale' => 'en']))
        ->assertOk()
        ->assertViewHas('user', fn ($u) => $u->id === $user->id);
}

public function test_tester_can_update_profile(): void
{
    $user = $this->testerUser();

    $this->actingAs($user)
        ->patch(route('tester.profile.update', ['locale' => 'en']), [
            'name'  => 'Updated Name',
            'phone' => '+237600000001',
        ])
        ->assertRedirect(route('tester.profile', ['locale' => 'en']));

    $this->assertDatabaseHas('users', [
        'id'    => $user->id,
        'name'  => 'Updated Name',
        'phone' => '+237600000001',
    ]);
}
```

- [ ] **Step 2.2 — Run tests to confirm they fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=TesterPortalEnhancedTest
```
Expected: 2 new tests FAIL — route not found.

- [ ] **Step 2.3 — Create ProfileController**

```php
// app/Http/Controllers/Tester/ProfileController.php
<?php
namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return view('tester.profile', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
        ]);

        $request->user()->update($validated);

        return redirect()
            ->route('tester.profile', ['locale' => app()->getLocale()])
            ->with('success', 'Profile updated successfully.');
    }
}
```

- [ ] **Step 2.4 — Add routes** (inside the existing `tester` group in `routes/web.php`)

Locate the block:
```php
Route::middleware(['auth', 'role:tester'])
    ->prefix('tester')
    ->name('tester.')
    ->group(function () {
        Route::get('/dashboard', ...)->name('dashboard');
        Route::get('/assignments', ...)->name('assignments');
        Route::get('/assignments/{id}', ...)->name('assignments.show');
        Route::patch('/assignments/{id}/status', ...)->name('assignments.status');
        Route::post('/assignments/{id}/bug-reports', ...)->name('assignments.bug-reports');
    });
```

Add two lines after the last route inside the group:
```php
Route::get('/profile',  [\App\Http\Controllers\Tester\ProfileController::class, 'show'])->name('profile');
Route::patch('/profile', [\App\Http\Controllers\Tester\ProfileController::class, 'update'])->name('profile.update');
```

- [ ] **Step 2.5 — Create the profile view**

```blade
{{-- resources/views/tester/profile.blade.php --}}
<x-layouts.tester title="My Profile">

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
    {{-- Avatar / info card --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 flex flex-col items-center text-center">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 text-2xl font-bold text-white"
             style="background:linear-gradient(135deg,#1A6FE8,#00C896)">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <p class="text-lg font-semibold text-white">{{ $user->name }}</p>
        <p class="text-sm text-slate-400 mb-1">{{ $user->email }}</p>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-900/40 text-blue-300 border border-blue-800 mt-2">
            Tester
        </span>
        <p class="text-xs text-slate-500 mt-4">Member since {{ $user->created_at->format('M Y') }}</p>
    </div>

    {{-- Edit form --}}
    <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h2 class="text-sm font-semibold text-white mb-5">Account Information</h2>
        <form method="POST" action="{{ route('tester.profile.update', ['locale' => app()->getLocale()]) }}">
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
                    @error('phone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Email (read-only)</label>
                    <input type="text" value="{{ $user->email }}" disabled
                           class="w-full bg-slate-800/50 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-slate-500 cursor-not-allowed">
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white"
                        style="background:linear-gradient(135deg,#1A6FE8,#1258c4)">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
</x-layouts.tester>
```

- [ ] **Step 2.6 — Run all tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=TesterPortalEnhancedTest
```
Expected: All 4 tests PASS.

- [ ] **Step 2.7 — Commit**

```
git add app/Http/Controllers/Tester/ProfileController.php resources/views/tester/profile.blade.php routes/web.php tests/Feature/TesterPortalEnhancedTest.php
git commit -m "feat(tester): add profile page with name/phone editing"
```

---

## Task 3: Tester Bug Reports Page

**Files:**
- Create: `app/Http/Controllers/Tester/BugReportController.php`
- Create: `resources/views/tester/bug-reports/index.blade.php`

- [ ] **Step 3.1 — Add bug report tests**

Append to `tests/Feature/TesterPortalEnhancedTest.php`:

```php
public function test_tester_bug_reports_page_loads(): void
{
    $user = $this->testerUser();

    Ticket::create([
        'user_id'     => $user->id,
        'subject'     => 'Button not clickable',
        'description' => 'The submit button fails on mobile',
        'type'        => 'bug_report',
        'status'      => 'open',
        'priority'    => 'high',
    ]);

    $this->actingAs($user)
        ->get(route('tester.bug-reports', ['locale' => 'en']))
        ->assertOk()
        ->assertSee('Button not clickable');
}
```

- [ ] **Step 3.2 — Create BugReportController**

```php
// app/Http/Controllers/Tester/BugReportController.php
<?php
namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class BugReportController extends Controller
{
    public function index()
    {
        $reports = Ticket::where('user_id', Auth::id())
            ->where('type', 'bug_report')
            ->with('testerAssignment')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('tester.bug-reports.index', compact('reports'));
    }
}
```

- [ ] **Step 3.3 — Add route** (inside the existing tester group, after the profile routes added in Task 2)

```php
Route::get('/bug-reports', [\App\Http\Controllers\Tester\BugReportController::class, 'index'])->name('bug-reports');
```

- [ ] **Step 3.4 — Create the view**

```blade
{{-- resources/views/tester/bug-reports/index.blade.php --}}
<x-layouts.tester title="My Bug Reports">

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">Bug Reports</h1>
        <p class="cp-page-subtitle">All bug reports you've filed across testing assignments</p>
    </div>
</div>

@if($reports->isEmpty())
<div class="bg-slate-900 border border-slate-800 rounded-xl p-10 text-center">
    <i data-lucide="bug" style="width:40px;height:40px;color:#334155;margin:0 auto 12px"></i>
    <p class="text-slate-400 text-sm">No bug reports filed yet.</p>
    <p class="text-slate-500 text-xs mt-1">Bug reports appear here when you file them on an assignment.</p>
</div>
@else
<div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="border-bottom:1px solid #1e293b">
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Subject</th>
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Assignment</th>
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Priority</th>
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Status</th>
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Filed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            @php
                $priorityColor = match($report->priority) {
                    'urgent' => '#ef4444',
                    'high'   => '#f97316',
                    'medium' => '#F59E0B',
                    default  => '#64748b',
                };
                $statusColor = match($report->status) {
                    'open'        => '#1A6FE8',
                    'in_progress' => '#F59E0B',
                    'resolved'    => '#00C896',
                    'closed'      => '#64748b',
                    default       => '#64748b',
                };
            @endphp
            <tr style="border-bottom:1px solid #0f172a">
                <td style="padding:.75rem 1rem;color:#e2e8f0;font-size:.875rem">{{ Str::limit($report->subject, 50) }}</td>
                <td style="padding:.75rem 1rem;color:#64748b;font-size:.8125rem">
                    {{ $report->testerAssignment?->product_name ?? '—' }}
                </td>
                <td style="padding:.75rem 1rem">
                    <span style="color:{{ $priorityColor }};font-size:.75rem;font-weight:700;text-transform:uppercase">
                        {{ ucfirst($report->priority) }}
                    </span>
                </td>
                <td style="padding:.75rem 1rem">
                    <span style="color:{{ $statusColor }};font-size:.75rem;font-weight:700;text-transform:uppercase">
                        {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                    </span>
                </td>
                <td style="padding:.75rem 1rem;color:#475569;font-size:.8125rem">
                    {{ $report->created_at->format('d M Y') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding:1rem">{{ $reports->links() }}</div>
</div>
@endif
</x-layouts.tester>
```

- [ ] **Step 3.5 — Run all tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=TesterPortalEnhancedTest
```
Expected: All 5 tests PASS.

- [ ] **Step 3.6 — Update tester nav layout** (add Profile + Bug Reports links)

In `resources/views/components/layouts/tester.blade.php`, find `<div class="cp-nav-links portal-menu">` and replace its contents:

```blade
<div class="cp-nav-links portal-menu">
    <a href="{{ route('tester.dashboard', ['locale' => app()->getLocale()]) }}"
       class="cp-nav-link {{ request()->routeIs('tester.dashboard') ? 'cp-nav-link-active' : '' }}">
        <i data-lucide="layout-dashboard" style="width:16px;height:16px"></i> Dashboard
    </a>
    <a href="{{ route('tester.assignments', ['locale' => app()->getLocale()]) }}"
       class="cp-nav-link {{ request()->routeIs('tester.assignments*') ? 'cp-nav-link-active' : '' }}">
        <i data-lucide="clipboard-list" style="width:16px;height:16px"></i> Assignments
    </a>
    <a href="{{ route('tester.bug-reports', ['locale' => app()->getLocale()]) }}"
       class="cp-nav-link {{ request()->routeIs('tester.bug-reports*') ? 'cp-nav-link-active' : '' }}">
        <i data-lucide="bug" style="width:16px;height:16px"></i> Bug Reports
    </a>
    <a href="{{ route('tester.profile', ['locale' => app()->getLocale()]) }}"
       class="cp-nav-link {{ request()->routeIs('tester.profile*') ? 'cp-nav-link-active' : '' }}">
        <i data-lucide="user" style="width:16px;height:16px"></i> Profile
    </a>
</div>
```

- [ ] **Step 3.7 — Commit**

```
git add app/Http/Controllers/Tester/BugReportController.php resources/views/tester/bug-reports/index.blade.php resources/views/components/layouts/tester.blade.php routes/web.php tests/Feature/TesterPortalEnhancedTest.php
git commit -m "feat(tester): add bug reports page + profile + nav links"
```

---

## Task 4: Support Portal — Layout + Dashboard

**Files:**
- Create: `resources/views/components/layouts/support.blade.php`
- Create: `app/Http/Controllers/Support/DashboardController.php`
- Create: `resources/views/support/dashboard.blade.php`
- Create: `tests/Feature/SupportPortalTest.php`

The support portal accent colour is **orange `#F97316`** to visually distinguish from admin.

- [ ] **Step 4.1 — Write failing tests**

```php
// tests/Feature/SupportPortalTest.php
<?php
namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportPortalTest extends TestCase
{
    use RefreshDatabase;

    private function supportUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('support');
        return $user;
    }

    public function test_support_dashboard_loads(): void
    {
        $support = $this->supportUser();

        Ticket::create([
            'assigned_to' => $support->id,
            'subject'     => 'Cannot log in',
            'description' => 'Customer reports login failure',
            'type'        => 'support',
            'status'      => 'open',
            'priority'    => 'high',
        ]);

        $this->actingAs($support)
            ->get(route('support.dashboard', ['locale' => 'en']))
            ->assertOk()
            ->assertViewHas('myOpenCount', 1)
            ->assertViewHas('myResolvedToday');
    }

    public function test_non_support_cannot_access_support_portal(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)
            ->get(route('support.dashboard', ['locale' => 'en']))
            ->assertForbidden();
    }
}
```

- [ ] **Step 4.2 — Run to confirm failure**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=SupportPortalTest
```
Expected: FAIL — route `support.dashboard` not found.

- [ ] **Step 4.3 — Create the layout**

```blade
{{-- resources/views/components/layouts/support.blade.php --}}
@props(['title' => 'Support Portal'])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — OPES Support</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cp-body">
<nav class="cp-nav" id="cp-portal-nav">
    <a href="{{ route('support.dashboard', ['locale' => app()->getLocale()]) }}" class="cp-nav-brand">
        <span class="cp-brand-opes">OPES</span>
        <span class="cp-brand-name" style="color:#F97316"> Support</span>
    </a>
    <button class="cp-burger" onclick="document.getElementById('cp-portal-nav').classList.toggle('nav-open')" aria-label="Menu">
        <i data-lucide="menu" style="width:22px;height:22px"></i>
    </button>
    <div class="cp-nav-body">
        <div class="cp-nav-links">
            <a href="{{ route('support.dashboard', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('support.dashboard') ? 'cp-nav-link-active' : '' }}"
               style="{{ request()->routeIs('support.dashboard') ? '--link-active-color:#F97316' : '' }}">
                <i data-lucide="layout-dashboard" style="width:16px;height:16px"></i> Dashboard
            </a>
            <a href="{{ route('support.tickets', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('support.tickets*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="ticket" style="width:16px;height:16px"></i> Ticket Queue
            </a>
        </div>
        <div class="cp-nav-user">
            <span class="cp-nav-username">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="cp-logout-btn" title="Sign out">
                    <i data-lucide="log-out" style="width:16px;height:16px"></i>
                </button>
            </form>
        </div>
    </div>
</nav>
@if(session('success'))
<div class="cp-flash-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="cp-flash-error">{{ session('error') }}</div>
@endif
<main class="cp-main">
    <div class="cp-container">{{ $slot }}</div>
</main>
<script src="{{ asset('vendor/lucide.min.js') }}"></script>
<script>lucide.createIcons();</script>
</body>
</html>
```

- [ ] **Step 4.4 — Create DashboardController**

```php
// app/Http/Controllers/Support/DashboardController.php
<?php
namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $me = Auth::id();

        $myOpenCount      = Ticket::where('assigned_to', $me)->whereIn('status', ['open', 'in_progress', 'pending_customer'])->count();
        $myResolvedToday  = Ticket::where('assigned_to', $me)->where('status', 'resolved')
                                  ->whereDate('resolved_at', today())->count();
        $unassignedCount  = Ticket::whereNull('assigned_to')->whereIn('status', ['open', 'in_progress'])->count();
        $slaBreachedCount = Ticket::where('assigned_to', $me)
                                  ->where('sla_resolution_due_at', '<', now())
                                  ->whereNotIn('status', ['resolved', 'closed'])
                                  ->count();

        $myQueue = Ticket::where('assigned_to', $me)
            ->whereIn('status', ['open', 'in_progress', 'pending_customer'])
            ->with('customer')
            ->orderByRaw("FIELD(priority,'urgent','high','medium','low')")
            ->take(8)
            ->get();

        $unassigned = Ticket::whereNull('assigned_to')
            ->whereIn('status', ['open'])
            ->with('customer')
            ->orderByRaw("FIELD(priority,'urgent','high','medium','low')")
            ->take(5)
            ->get();

        return view('support.dashboard', compact(
            'myOpenCount', 'myResolvedToday', 'unassignedCount',
            'slaBreachedCount', 'myQueue', 'unassigned'
        ));
    }
}
```

- [ ] **Step 4.5 — Add routes** (inside the `{locale}` prefix group, after the accountant group)

```php
// Support portal
Route::middleware(['auth', 'role:support'])->prefix('support')->name('support.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Support\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/tickets',   [\App\Http\Controllers\Support\TicketController::class,    'index'])->name('tickets');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\Support\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply',  [\App\Http\Controllers\Support\TicketController::class, 'reply'])->name('tickets.reply');
    Route::patch('/tickets/{ticket}/status', [\App\Http\Controllers\Support\TicketController::class, 'updateStatus'])->name('tickets.status');
    Route::patch('/tickets/{ticket}/assign', [\App\Http\Controllers\Support\TicketController::class, 'assignToMe'])->name('tickets.assign');
});
```

- [ ] **Step 4.6 — Create the dashboard view**

```blade
{{-- resources/views/support/dashboard.blade.php --}}
<x-layouts.support title="Dashboard">
@php $locale = app()->getLocale(); @endphp

<div class="flex items-start justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-bold text-white mb-0.5">Support Dashboard</h1>
        <p class="text-slate-400 text-sm">Welcome back, {{ auth()->user()->name }}</p>
    </div>
    <a href="{{ route('support.tickets', ['locale' => $locale]) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
       style="background:#F97316">
        <i data-lucide="inbox" style="width:15px;height:15px"></i> View Full Queue
    </a>
</div>

{{-- KPI row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    @php
    $kpis = [
        ['label'=>'My Open Tickets',   'value'=>$myOpenCount,      'icon'=>'inbox',         'color'=>'#F97316'],
        ['label'=>'Resolved Today',    'value'=>$myResolvedToday,  'icon'=>'check-circle',  'color'=>'#00C896'],
        ['label'=>'Unassigned',        'value'=>$unassignedCount,  'icon'=>'alert-circle',  'color'=>'#F59E0B'],
        ['label'=>'SLA Breached',      'value'=>$slaBreachedCount, 'icon'=>'clock',         'color'=>'#ef4444'],
    ];
    @endphp
    @foreach($kpis as $kpi)
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0"
             style="background:{{ $kpi['color'] }}1a">
            <i data-lucide="{{ $kpi['icon'] }}" style="width:22px;height:22px;color:{{ $kpi['color'] }}"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-white">{{ $kpi['value'] }}</p>
            <p class="text-xs text-slate-400">{{ $kpi['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- My queue --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h2 class="font-semibold text-white text-sm mb-4 flex items-center gap-2">
            <i data-lucide="inbox" style="width:16px;height:16px;color:#F97316"></i> My Queue
        </h2>
        @forelse($myQueue as $ticket)
        @php
            $pc = match($ticket->priority) { 'urgent'=>'#ef4444','high'=>'#f97316','medium'=>'#F59E0B', default=>'#64748b' };
        @endphp
        <div class="flex items-start justify-between gap-3 mb-3 pb-3 border-b border-slate-800 last:border-0 last:mb-0 last:pb-0">
            <div>
                <p class="text-sm text-slate-200 font-medium">{{ Str::limit($ticket->subject, 45) }}</p>
                <p class="text-xs text-slate-500">{{ $ticket->customer?->name ?? 'Unknown' }}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span style="color:{{ $pc }};font-size:.6875rem;font-weight:700;text-transform:uppercase">{{ $ticket->priority }}</span>
                <a href="{{ route('support.tickets.show', ['locale'=>$locale,'ticket'=>$ticket->id]) }}"
                   class="text-xs text-orange-400 hover:underline no-underline">Open →</a>
            </div>
        </div>
        @empty
        <p class="text-slate-500 text-sm text-center py-4">Your queue is clear.</p>
        @endforelse
    </div>

    {{-- Unassigned --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h2 class="font-semibold text-white text-sm mb-4 flex items-center gap-2">
            <i data-lucide="alert-circle" style="width:16px;height:16px;color:#F59E0B"></i> Unassigned Tickets
        </h2>
        @forelse($unassigned as $ticket)
        @php $pc = match($ticket->priority) { 'urgent'=>'#ef4444','high'=>'#f97316','medium'=>'#F59E0B', default=>'#64748b' }; @endphp
        <div class="flex items-start justify-between gap-3 mb-3 pb-3 border-b border-slate-800 last:border-0 last:mb-0 last:pb-0">
            <div>
                <p class="text-sm text-slate-200 font-medium">{{ Str::limit($ticket->subject, 45) }}</p>
                <p class="text-xs text-slate-500">{{ $ticket->customer?->name ?? 'Unknown' }}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span style="color:{{ $pc }};font-size:.6875rem;font-weight:700;text-transform:uppercase">{{ $ticket->priority }}</span>
                <form method="POST" action="{{ route('support.tickets.assign', ['locale'=>$locale,'ticket'=>$ticket->id]) }}" style="margin:0">
                    @csrf @method('PATCH')
                    <button type="submit" style="font-size:.75rem;color:#F97316;background:none;border:none;cursor:pointer;padding:0">
                        Claim →
                    </button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-slate-500 text-sm text-center py-4">No unassigned tickets.</p>
        @endforelse
    </div>
</div>
</x-layouts.support>
```

- [ ] **Step 4.7 — Run tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=SupportPortalTest
```
Expected: Both tests PASS (dashboard loads, non-support gets 403).

- [ ] **Step 4.8 — Commit**

```
git add app/Http/Controllers/Support/DashboardController.php resources/views/components/layouts/support.blade.php resources/views/support/dashboard.blade.php routes/web.php tests/Feature/SupportPortalTest.php
git commit -m "feat(support): add support portal layout + dashboard with KPI queue"
```

---

## Task 5: Support Ticket Queue + Show

**Files:**
- Create: `app/Http/Controllers/Support/TicketController.php`
- Create: `resources/views/support/tickets/index.blade.php`
- Create: `resources/views/support/tickets/show.blade.php`

- [ ] **Step 5.1 — Add ticket controller tests**

Append to `tests/Feature/SupportPortalTest.php`:

```php
public function test_support_can_view_ticket_list(): void
{
    $support = $this->supportUser();
    Ticket::create([
        'assigned_to' => $support->id,
        'subject'     => 'Screen flicker',
        'description' => 'Dashboard flickers on load',
        'type'        => 'support',
        'status'      => 'open',
        'priority'    => 'medium',
    ]);

    $this->actingAs($support)
        ->get(route('support.tickets', ['locale' => 'en']))
        ->assertOk()
        ->assertSee('Screen flicker');
}

public function test_support_can_add_reply(): void
{
    $support = $this->supportUser();
    $ticket  = Ticket::create([
        'assigned_to' => $support->id,
        'subject'     => 'Password reset broken',
        'description' => 'User cannot reset password',
        'type'        => 'support',
        'status'      => 'open',
        'priority'    => 'high',
    ]);

    $this->actingAs($support)
        ->post(route('support.tickets.reply', ['locale' => 'en', 'ticket' => $ticket->id]), [
            'body'        => 'We are looking into this now.',
            'is_internal' => false,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('ticket_replies', [
        'ticket_id' => $ticket->id,
        'user_id'   => $support->id,
        'body'      => 'We are looking into this now.',
    ]);
}
```

- [ ] **Step 5.2 — Create TicketController**

```php
// app/Http/Controllers/Support/TicketController.php
<?php
namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with('customer')
            ->orderByRaw("FIELD(priority,'urgent','high','medium','low')")
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('mine')) {
            $query->where('assigned_to', Auth::id());
        }

        $tickets = $query->paginate(25)->withQueryString();

        return view('support.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['customer', 'assignee', 'publicReplies.author', 'testerAssignment']);
        return view('support.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'body'        => 'required|string|max:10000',
            'is_internal' => 'boolean',
        ]);

        TicketReply::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => Auth::id(),
            'body'        => $validated['body'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return redirect()
            ->route('support.tickets.show', ['locale' => app()->getLocale(), 'ticket' => $ticket->id])
            ->with('success', 'Reply added.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,pending_customer,resolved,closed',
        ]);

        $ticket->update(['status' => $validated['status']]);

        return redirect()
            ->route('support.tickets.show', ['locale' => app()->getLocale(), 'ticket' => $ticket->id])
            ->with('success', 'Status updated.');
    }

    public function assignToMe(Request $request, Ticket $ticket)
    {
        $ticket->update(['assigned_to' => Auth::id(), 'status' => 'in_progress']);

        return redirect()
            ->route('support.tickets.show', ['locale' => app()->getLocale(), 'ticket' => $ticket->id])
            ->with('success', 'Ticket assigned to you.');
    }
}
```

- [ ] **Step 5.3 — Create ticket list view**

```blade
{{-- resources/views/support/tickets/index.blade.php --}}
<x-layouts.support title="Ticket Queue">
@php $locale = app()->getLocale(); @endphp

<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="cp-page-title">Ticket Queue</h1>
        <p class="cp-page-subtitle">All platform tickets</p>
    </div>
    <form method="GET" class="flex gap-2 flex-wrap">
        <select name="status" onchange="this.form.submit()"
                class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none">
            <option value="">All Statuses</option>
            @foreach(\App\Models\Ticket::statusOptions() as $val => $label)
            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <label class="flex items-center gap-2 text-sm text-slate-400 bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 cursor-pointer">
            <input type="checkbox" name="mine" value="1" {{ request('mine') ? 'checked' : '' }} onchange="this.form.submit()">
            Mine only
        </label>
    </form>
</div>

<div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="border-bottom:1px solid #1e293b">
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Subject</th>
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">From</th>
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Priority</th>
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Status</th>
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Created</th>
                <th style="padding:.75rem 1rem"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            @php
                $pc = match($ticket->priority) { 'urgent'=>'#ef4444','high'=>'#f97316','medium'=>'#F59E0B', default=>'#64748b' };
                $sc = match($ticket->status) { 'open'=>'#1A6FE8','in_progress'=>'#F59E0B','resolved','closed'=>'#00C896', default=>'#64748b' };
            @endphp
            <tr style="border-bottom:1px solid #0f172a">
                <td style="padding:.75rem 1rem;color:#e2e8f0;font-size:.875rem">{{ Str::limit($ticket->subject, 50) }}</td>
                <td style="padding:.75rem 1rem;color:#64748b;font-size:.8125rem">{{ $ticket->customer?->name ?? '—' }}</td>
                <td style="padding:.75rem 1rem"><span style="color:{{ $pc }};font-size:.75rem;font-weight:700;text-transform:uppercase">{{ $ticket->priority }}</span></td>
                <td style="padding:.75rem 1rem"><span style="color:{{ $sc }};font-size:.75rem;font-weight:700;text-transform:uppercase">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></td>
                <td style="padding:.75rem 1rem;color:#475569;font-size:.8125rem">{{ $ticket->created_at->format('d M Y') }}</td>
                <td style="padding:.75rem 1rem;text-align:right">
                    <a href="{{ route('support.tickets.show', ['locale'=>$locale,'ticket'=>$ticket->id]) }}"
                       style="font-size:.75rem;color:#F97316;text-decoration:none;font-weight:600">Open →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:3rem;text-align:center;color:#475569">No tickets found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:1rem">{{ $tickets->links() }}</div>
</div>
</x-layouts.support>
```

- [ ] **Step 5.4 — Create ticket show view**

```blade
{{-- resources/views/support/tickets/show.blade.php --}}
<x-layouts.support title="{{ $ticket->subject }}">
@php $locale = app()->getLocale(); @endphp

<div class="flex items-start justify-between mb-6 flex-wrap gap-3">
    <div>
        <a href="{{ route('support.tickets', ['locale'=>$locale]) }}" class="text-xs text-slate-500 hover:text-slate-300 no-underline">← Back to Queue</a>
        <h1 class="text-xl font-bold text-white mt-1">{{ $ticket->subject }}</h1>
        <div class="flex items-center gap-2 mt-1 flex-wrap">
            @php
                $pc = match($ticket->priority) { 'urgent'=>'#ef4444','high'=>'#f97316','medium'=>'#F59E0B', default=>'#64748b' };
            @endphp
            <span style="color:{{ $pc }};font-size:.6875rem;font-weight:700;text-transform:uppercase">{{ $ticket->priority }}</span>
            <span class="text-slate-600">·</span>
            <span class="text-slate-400 text-xs">{{ $ticket->customer?->name ?? 'Unknown customer' }}</span>
            <span class="text-slate-600">·</span>
            <span class="text-slate-400 text-xs">{{ $ticket->created_at->format('d M Y H:i') }}</span>
        </div>
    </div>
    {{-- Status change --}}
    <form method="POST" action="{{ route('support.tickets.status', ['locale'=>$locale,'ticket'=>$ticket->id]) }}" class="flex items-center gap-2">
        @csrf @method('PATCH')
        <select name="status"
                class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none">
            @foreach(\App\Models\Ticket::statusOptions() as $val => $label)
            <option value="{{ $val }}" {{ $ticket->status === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#F97316">
            Update
        </button>
    </form>
</div>

@if(!$ticket->assignee)
<div class="bg-amber-900/20 border border-amber-700 rounded-lg px-5 py-3 mb-6 flex items-center justify-between">
    <p class="text-amber-300 text-sm">This ticket is unassigned.</p>
    <form method="POST" action="{{ route('support.tickets.assign', ['locale'=>$locale,'ticket'=>$ticket->id]) }}">
        @csrf @method('PATCH')
        <button type="submit" class="text-sm font-semibold text-amber-300 hover:text-amber-100">Claim it →</button>
    </form>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Thread --}}
    <div class="lg:col-span-2 flex flex-col gap-4">
        {{-- Original message --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs font-semibold text-slate-400">{{ $ticket->customer?->name ?? 'Customer' }}</span>
                <span class="text-xs text-slate-600">{{ $ticket->created_at->format('d M Y H:i') }}</span>
            </div>
            <p class="text-slate-300 text-sm whitespace-pre-wrap">{{ $ticket->description }}</p>
        </div>

        {{-- Replies --}}
        @foreach($ticket->publicReplies as $reply)
        @php $isSupport = $reply->author?->hasAnyRole(['super_admin','admin','support']); @endphp
        <div class="rounded-xl p-5 border {{ $isSupport ? 'border-orange-900/50 bg-orange-900/10' : 'bg-slate-900 border-slate-800' }}">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs font-semibold {{ $isSupport ? 'text-orange-300' : 'text-slate-400' }}">
                    {{ $reply->author?->name ?? 'Staff' }}
                    @if($isSupport) <span class="font-normal text-orange-500/70">(Support)</span> @endif
                </span>
                <span class="text-xs text-slate-600">{{ $reply->created_at->format('d M Y H:i') }}</span>
            </div>
            <p class="text-slate-300 text-sm whitespace-pre-wrap">{{ $reply->body }}</p>
        </div>
        @endforeach

        {{-- Reply form --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <h3 class="text-sm font-semibold text-white mb-3">Add Reply</h3>
            <form method="POST" action="{{ route('support.tickets.reply', ['locale'=>$locale,'ticket'=>$ticket->id]) }}">
                @csrf
                <textarea name="body" rows="4" required
                          class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-orange-500 resize-none"
                          placeholder="Type your reply…"></textarea>
                <div class="flex items-center justify-between mt-3">
                    <label class="flex items-center gap-2 text-xs text-slate-400 cursor-pointer">
                        <input type="checkbox" name="is_internal" value="1" class="rounded">
                        Internal note (hidden from customer)
                    </label>
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#F97316">
                        Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="flex flex-col gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Details</h3>
            <dl class="flex flex-col gap-2.5 text-sm">
                <div><dt class="text-slate-500 text-xs">Type</dt><dd class="text-slate-300">{{ ucfirst($ticket->type) }}</dd></div>
                <div><dt class="text-slate-500 text-xs">Assigned to</dt><dd class="text-slate-300">{{ $ticket->assignee?->name ?? 'Unassigned' }}</dd></div>
                @if($ticket->sla_resolution_due_at)
                <div>
                    <dt class="text-slate-500 text-xs">SLA deadline</dt>
                    <dd class="{{ $ticket->isSlaResolutionBreached() ? 'text-red-400 font-semibold' : 'text-slate-300' }}">
                        {{ $ticket->sla_resolution_due_at->format('d M Y H:i') }}
                        @if($ticket->isSlaResolutionBreached()) (breached) @endif
                    </dd>
                </div>
                @endif
                @if($ticket->testerAssignment)
                <div>
                    <dt class="text-slate-500 text-xs">Assignment</dt>
                    <dd class="text-slate-300 text-xs">{{ $ticket->testerAssignment->title }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>
</div>
</x-layouts.support>
```

- [ ] **Step 5.5 — Run all tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=SupportPortalTest
```
Expected: All 4 tests PASS.

- [ ] **Step 5.6 — Commit**

```
git add app/Http/Controllers/Support/ resources/views/support/ tests/Feature/SupportPortalTest.php
git commit -m "feat(support): add ticket queue + show with reply, assign, status update"
```

---

## Task 6: Practitioner Directory — Public Listing

**Files:**
- Create: `app/Http/Controllers/Public/PractitionerDirectoryController.php`
- Create: `resources/views/pages/practitioners/index.blade.php`
- Create: `tests/Feature/PractitionerDirectoryTest.php`

Only practitioners with a `PractitionerProfile` AND at least one `approved` application are listed. No sensitive fields (payout_number, registration_number) are exposed.

- [ ] **Step 6.1 — Write failing tests**

```php
// tests/Feature/PractitionerDirectoryTest.php
<?php
namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProfile;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PractitionerDirectoryTest extends TestCase
{
    use RefreshDatabase;

    private function makePractitioner(array $profileOverrides = []): User
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        PractitionerProfile::create(array_merge([
            'user_id'            => $user->id,
            'profession'         => 'doctor',
            'specialty'          => 'Cardiology',
            'workplace_name'     => 'City Hospital',
            'workplace_country'  => 'Cameroon',
            'bio'                => 'Experienced cardiologist.',
            'years_of_experience'=> 10,
            'is_verified'        => false,
        ], $profileOverrides));
        return $user;
    }

    public function test_directory_lists_practitioners_with_approved_application(): void
    {
        $user    = $this->makePractitioner();
        $program = PractitionerProgram::create([
            'product_slug' => 'opes-clinic', 'product_name' => 'OPES Clinic',
            'title' => 'Pilot Review', 'type' => 'volunteer', 'status' => 'open',
        ]);
        PractitionerApplication::create([
            'practitioner_id' => $user->id,
            'program_id'      => $program->id,
            'status'          => 'approved',
        ]);

        $this->get(route('practitioners.index', ['locale' => 'en']))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee('Cardiology');
    }

    public function test_directory_excludes_practitioners_with_no_approved_application(): void
    {
        $user = $this->makePractitioner();
        // No approved application

        $this->get(route('practitioners.index', ['locale' => 'en']))
            ->assertOk()
            ->assertDontSee($user->name);
    }

    public function test_directory_filters_by_profession(): void
    {
        $doctor = $this->makePractitioner(['profession' => 'doctor']);
        $nurse  = $this->makePractitioner(['profession' => 'nurse']);
        $program = PractitionerProgram::create([
            'product_slug' => 'opes-clinic', 'product_name' => 'OPES Clinic',
            'title' => 'Pilot', 'type' => 'volunteer', 'status' => 'open',
        ]);
        foreach ([$doctor, $nurse] as $u) {
            PractitionerApplication::create(['practitioner_id'=>$u->id,'program_id'=>$program->id,'status'=>'approved']);
        }

        $response = $this->get(route('practitioners.index', ['locale'=>'en','profession'=>'nurse']));
        $response->assertSee($nurse->name)->assertDontSee($doctor->name);
    }

    public function test_directory_does_not_expose_payout_number(): void
    {
        $user = $this->makePractitioner(['payout_number' => 'MOMO-12345']);
        $program = PractitionerProgram::create([
            'product_slug'=>'opes-clinic','product_name'=>'OPES Clinic','title'=>'Pilot','type'=>'volunteer','status'=>'open',
        ]);
        PractitionerApplication::create(['practitioner_id'=>$user->id,'program_id'=>$program->id,'status'=>'approved']);

        $this->get(route('practitioners.index', ['locale' => 'en']))
            ->assertDontSee('MOMO-12345');
    }
}
```

- [ ] **Step 6.2 — Run tests to confirm failure**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=PractitionerDirectoryTest
```
Expected: All 4 FAIL — route not found.

- [ ] **Step 6.3 — Create the controller**

```php
// app/Http/Controllers/Public/PractitionerDirectoryController.php
<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PractitionerApplication;
use App\Models\PractitionerProfile;
use Illuminate\Http\Request;

class PractitionerDirectoryController extends Controller
{
    public function index(Request $request)
    {
        // Only list practitioners who have at least one approved application
        $approvedIds = PractitionerApplication::where('status', 'approved')
            ->pluck('practitioner_id')
            ->unique();

        $query = PractitionerProfile::whereIn('user_id', $approvedIds)
            ->with('user')
            ->orderByDesc('is_verified')
            ->orderByDesc('years_of_experience');

        if ($request->filled('profession')) {
            $query->where('profession', $request->profession);
        }
        if ($request->filled('country')) {
            $query->where('workplace_country', $request->country);
        }

        $practitioners = $query->paginate(12)->withQueryString();

        // Aggregate stats per practitioner
        $stats = [];
        foreach ($practitioners as $profile) {
            $applications    = PractitionerApplication::where('practitioner_id', $profile->user_id)->where('status', 'approved')->count();
            $findingsCount   = \App\Models\PractitionerFinding::where('practitioner_id', $profile->user_id)->where('is_published', true)->count();
            $avgRating       = \App\Models\PractitionerFinding::where('practitioner_id', $profile->user_id)->whereNotNull('overall_rating')->avg('overall_rating');
            $stats[$profile->user_id] = [
                'programs'  => $applications,
                'findings'  => $findingsCount,
                'avgRating' => $avgRating ? round($avgRating, 1) : null,
            ];
        }

        $professions = PractitionerProfile::professionOptions();
        $countries   = PractitionerProfile::whereIn('user_id', $approvedIds)
            ->whereNotNull('workplace_country')
            ->distinct()
            ->orderBy('workplace_country')
            ->pluck('workplace_country');

        return view('pages.practitioners.index', compact('practitioners', 'stats', 'professions', 'countries'));
    }

    public function show(string $locale, int $id)
    {
        $profile = PractitionerProfile::where('user_id', $id)
            ->whereHas('user.practitionerApplications', fn ($q) => $q->where('status', 'approved'))
            ->with('user')
            ->firstOrFail();

        $approvedApplications = PractitionerApplication::where('practitioner_id', $id)
            ->where('status', 'approved')
            ->with('program')
            ->orderByDesc('reviewed_at')
            ->get();

        $publishedFindings = \App\Models\PractitionerFinding::where('practitioner_id', $id)
            ->where('is_published', true)
            ->with('application.program')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $ratingBreakdown = [
            'overall'        => $publishedFindings->avg('overall_rating'),
            'usability'      => $publishedFindings->avg('usability_rating'),
            'wait_time'      => $publishedFindings->avg('wait_time_rating'),
            'data_integrity' => $publishedFindings->avg('data_integrity_rating'),
        ];

        return view('pages.practitioners.show', compact(
            'profile', 'approvedApplications', 'publishedFindings', 'ratingBreakdown'
        ));
    }
}
```

> **Note:** The `show()` method uses `user.practitionerApplications` relationship. Add this to `User.php` if not present:
> `public function practitionerApplications(): HasMany { return $this->hasMany(PractitionerApplication::class, 'practitioner_id'); }`

- [ ] **Step 6.4 — Add public routes** (inside the `{locale}` group, before closing brace)

```php
// Public practitioner directory
Route::get('/practitioners',      [\App\Http\Controllers\Public\PractitionerDirectoryController::class, 'index'])->name('practitioners.index');
Route::get('/practitioners/{id}', [\App\Http\Controllers\Public\PractitionerDirectoryController::class, 'show'])->name('practitioners.show');
```

- [ ] **Step 6.5 — Add `practitionerApplications()` relationship to User model** if missing

In `app/Models/User.php`, in the relationships section, add:
```php
public function practitionerApplications(): HasMany
{
    return $this->hasMany(PractitionerApplication::class, 'practitioner_id');
}
```

- [ ] **Step 6.6 — Create the directory listing view**

```blade
{{-- resources/views/pages/practitioners/index.blade.php --}}
<x-layouts.app>
@php $locale = app()->getLocale(); @endphp
<section class="py-16 px-4" style="min-height:80vh">
    <div style="max-width:1200px;margin:0 auto">

        {{-- Hero --}}
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold text-emerald-400 uppercase tracking-widest mb-3">Verified Contributors</span>
            <h1 class="text-4xl font-bold text-white mb-4">Practitioner Directory</h1>
            <p class="text-slate-400 text-lg max-w-xl mx-auto">
                Healthcare professionals who have participated in OPES product testing programs and contributed findings.
            </p>
        </div>

        {{-- Filters --}}
        <form method="GET" class="flex flex-wrap gap-3 mb-8 justify-center">
            <select name="profession" onchange="this.form.submit()"
                    class="bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-emerald-500">
                <option value="">All Professions</option>
                @foreach($professions as $key => $label)
                <option value="{{ $key }}" {{ request('profession') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="country" onchange="this.form.submit()"
                    class="bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-emerald-500">
                <option value="">All Countries</option>
                @foreach($countries as $country)
                <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>{{ $country }}</option>
                @endforeach
            </select>
            @if(request()->hasAny(['profession','country']))
            <a href="{{ route('practitioners.index', ['locale' => $locale]) }}"
               class="flex items-center gap-1 text-sm text-slate-400 hover:text-white bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 no-underline">
                Clear filters
            </a>
            @endif
        </form>

        {{-- Grid --}}
        @if($practitioners->isEmpty())
        <div class="text-center py-20 text-slate-500">
            <i data-lucide="users" style="width:48px;height:48px;margin:0 auto 12px"></i>
            <p>No practitioners found for the selected filters.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            @foreach($practitioners as $profile)
            @php
                $s    = $stats[$profile->user_id] ?? ['programs'=>0,'findings'=>0,'avgRating'=>null];
                $tier = $profile->user?->practitionerTier();
            @endphp
            <a href="{{ route('practitioners.show', ['locale'=>$locale,'id'=>$profile->user_id]) }}"
               class="bg-slate-900 border border-slate-800 hover:border-emerald-700 rounded-xl p-6 flex flex-col gap-4 transition-colors no-underline group">
                {{-- Avatar + badge --}}
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#00C896,#1A6FE8)">
                        {{ strtoupper(substr($profile->user->name, 0, 1)) }}
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        @if($profile->is_verified)
                        <span class="flex items-center gap-1 text-xs font-semibold text-emerald-400 bg-emerald-900/30 px-2 py-0.5 rounded-full border border-emerald-800">
                            <i data-lucide="shield-check" style="width:12px;height:12px"></i> Verified
                        </span>
                        @endif
                        @if($tier)
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $tier->tailwindBadge() }}">{{ $tier->label() }}</span>
                        @endif
                    </div>
                </div>

                {{-- Name + role --}}
                <div>
                    <p class="font-semibold text-white group-hover:text-emerald-300 transition-colors">{{ $profile->user->name }}</p>
                    <p class="text-sm text-slate-400">
                        {{ \App\Models\PractitionerProfile::professionOptions()[$profile->profession] ?? $profile->profession }}
                        @if($profile->specialty) · {{ $profile->specialty }} @endif
                    </p>
                    @if($profile->workplace_name)
                    <p class="text-xs text-slate-500 mt-1">{{ $profile->workplace_name }}, {{ $profile->workplace_country }}</p>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="flex gap-4 pt-3 border-t border-slate-800 text-center">
                    <div class="flex-1">
                        <p class="text-lg font-bold text-white">{{ $s['programs'] }}</p>
                        <p class="text-xs text-slate-500">Programs</p>
                    </div>
                    <div class="flex-1">
                        <p class="text-lg font-bold text-white">{{ $s['findings'] }}</p>
                        <p class="text-xs text-slate-500">Findings</p>
                    </div>
                    <div class="flex-1">
                        <p class="text-lg font-bold text-white">{{ $s['avgRating'] ? number_format($s['avgRating'],1) : '—' }}</p>
                        <p class="text-xs text-slate-500">Avg Rating</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div class="flex justify-center">{{ $practitioners->links() }}</div>
        @endif
    </div>
</section>
</x-layouts.app>
```

- [ ] **Step 6.7 — Run tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=PractitionerDirectoryTest
```
Expected: All 4 tests PASS.

- [ ] **Step 6.8 — Commit**

```
git add app/Http/Controllers/Public/PractitionerDirectoryController.php resources/views/pages/practitioners/index.blade.php routes/web.php tests/Feature/PractitionerDirectoryTest.php app/Models/User.php
git commit -m "feat(directory): public practitioner directory with profession/country filters"
```

---

## Task 7: Practitioner Directory — Individual Profile Page

**Files:**
- Create: `resources/views/pages/practitioners/show.blade.php`

- [ ] **Step 7.1 — Add show-page tests**

Append to `tests/Feature/PractitionerDirectoryTest.php`:

```php
public function test_practitioner_profile_page_loads(): void
{
    $user    = $this->makePractitioner(['bio' => 'I am a cardiologist with 10 years of experience.']);
    $program = PractitionerProgram::create([
        'product_slug' => 'opes-clinic', 'product_name' => 'OPES Clinic',
        'title' => 'Clinical Review', 'type' => 'volunteer', 'status' => 'open',
    ]);
    PractitionerApplication::create(['practitioner_id'=>$user->id,'program_id'=>$program->id,'status'=>'approved']);

    $this->get(route('practitioners.show', ['locale'=>'en', 'id'=>$user->id]))
        ->assertOk()
        ->assertSee($user->name)
        ->assertSee('I am a cardiologist');
}

public function test_practitioner_profile_page_404_when_no_approved_app(): void
{
    $user = $this->makePractitioner();
    // No approved application

    $this->get(route('practitioners.show', ['locale'=>'en', 'id'=>$user->id]))
        ->assertNotFound();
}
```

- [ ] **Step 7.2 — Run to confirm failure**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=PractitionerDirectoryTest
```
Expected: 2 new tests FAIL — view not found.

- [ ] **Step 7.3 — Create the profile show view**

```blade
{{-- resources/views/pages/practitioners/show.blade.php --}}
<x-layouts.app>
@php $locale = app()->getLocale(); @endphp
<section class="py-16 px-4" style="min-height:80vh">
<div style="max-width:900px;margin:0 auto">

    <a href="{{ route('practitioners.index', ['locale'=>$locale]) }}"
       class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-white no-underline mb-8">
        <i data-lucide="arrow-left" style="width:15px;height:15px"></i> Back to Directory
    </a>

    {{-- Hero card --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 mb-6 flex flex-col sm:flex-row gap-6">
        <div class="w-20 h-20 rounded-full flex items-center justify-center text-2xl font-bold text-white flex-shrink-0"
             style="background:linear-gradient(135deg,#00C896,#1A6FE8)">
            {{ strtoupper(substr($profile->user->name, 0, 1)) }}
        </div>
        <div class="flex-1">
            <div class="flex items-start justify-between flex-wrap gap-2 mb-1">
                <h1 class="text-2xl font-bold text-white">{{ $profile->user->name }}</h1>
                <div class="flex gap-2 flex-wrap">
                    @if($profile->is_verified)
                    <span class="flex items-center gap-1 text-xs font-semibold text-emerald-400 bg-emerald-900/30 px-2.5 py-1 rounded-full border border-emerald-800">
                        <i data-lucide="shield-check" style="width:12px;height:12px"></i> Verified Practitioner
                    </span>
                    @endif
                    @php $tier = $profile->user?->practitionerTier(); @endphp
                    @if($tier)
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $tier->tailwindBadge() }}">{{ $tier->label() }}</span>
                    @endif
                </div>
            </div>
            <p class="text-slate-300 font-medium">
                {{ \App\Models\PractitionerProfile::professionOptions()[$profile->profession] ?? $profile->profession }}
                @if($profile->specialty) · {{ $profile->specialty }} @endif
            </p>
            @if($profile->workplace_name)
            <p class="text-sm text-slate-500 mt-0.5">{{ $profile->workplace_name }} · {{ $profile->workplace_city }}, {{ $profile->workplace_country }}</p>
            @endif
            @if($profile->years_of_experience)
            <p class="text-sm text-slate-500 mt-0.5">{{ $profile->years_of_experience }} years of experience</p>
            @endif
        </div>
    </div>

    {{-- Contribution Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @php
            $totalPrograms  = $approvedApplications->count();
            $totalFindings  = $publishedFindings->count();
        @endphp
        @foreach([
            ['Programs Completed', $totalPrograms, '#00C896'],
            ['Findings Published', $totalFindings, '#1A6FE8'],
            ['Avg Overall Rating', $ratingBreakdown['overall'] ? number_format($ratingBreakdown['overall'],1) : '—', '#F59E0B'],
            ['Avg Usability',      $ratingBreakdown['usability'] ? number_format($ratingBreakdown['usability'],1) : '—', '#8B5CF6'],
        ] as [$label, $value, $color])
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-white">{{ $value }}</p>
            <p class="text-xs text-slate-400 mt-0.5">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Bio --}}
            @if($profile->bio)
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h2 class="font-semibold text-white text-sm mb-3">About</h2>
                <p class="text-slate-300 text-sm leading-relaxed">{{ $profile->bio }}</p>
            </div>
            @endif

            {{-- OPES testimonial --}}
            @if($profile->opes_testimonial)
            <div class="bg-emerald-900/10 border border-emerald-800 rounded-xl p-6">
                <h2 class="font-semibold text-emerald-300 text-sm mb-2 flex items-center gap-2">
                    <i data-lucide="quote" style="width:15px;height:15px"></i> On OPES Platform
                </h2>
                <p class="text-slate-300 text-sm leading-relaxed italic">"{{ $profile->opes_testimonial }}"</p>
            </div>
            @endif

            {{-- Programs participated --}}
            @if($approvedApplications->isNotEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h2 class="font-semibold text-white text-sm mb-4">Programs Participated</h2>
                <div class="flex flex-col gap-3">
                    @foreach($approvedApplications as $app)
                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-800 last:border-0 last:pb-0">
                        <div>
                            <p class="text-sm text-slate-200 font-medium">{{ $app->program->title ?? 'Program' }}</p>
                            <p class="text-xs text-slate-500">{{ $app->program->product_name ?? '' }}
                                @if($app->program) · {{ ucfirst($app->program->type) }} @endif
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="text-xs text-emerald-400 font-semibold">Approved</span>
                            @if($app->reviewed_at)
                            <span class="text-xs text-slate-600">{{ $app->reviewed_at->format('M Y') }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar: Rating breakdown + findings preview --}}
        <div class="flex flex-col gap-4">
            @if($totalFindings > 0)
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-4">Rating Breakdown</h3>
                @foreach([
                    ['Overall',        $ratingBreakdown['overall']],
                    ['Usability',      $ratingBreakdown['usability']],
                    ['Wait Time',      $ratingBreakdown['wait_time']],
                    ['Data Integrity', $ratingBreakdown['data_integrity']],
                ] as [$label, $val])
                @if($val)
                <div class="mb-3">
                    <div class="flex justify-between text-xs text-slate-400 mb-1">
                        <span>{{ $label }}</span>
                        <span class="text-white font-semibold">{{ number_format($val, 1) }}/5</span>
                    </div>
                    <div style="height:4px;background:#1e293b;border-radius:2px">
                        <div style="height:4px;width:{{ ($val/5)*100 }}%;background:linear-gradient(90deg,#00C896,#1A6FE8);border-radius:2px"></div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif

            @if($publishedFindings->isNotEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Recent Findings</h3>
                @foreach($publishedFindings->take(3) as $finding)
                <div class="mb-3 pb-3 border-b border-slate-800 last:border-0 last:pb-0 last:mb-0">
                    <p class="text-xs text-slate-500 mb-1">{{ $finding->application->program->product_name ?? 'Program' }}</p>
                    <p class="text-xs text-slate-300 line-clamp-2">{{ Str::limit($finding->findings_text, 90) }}</p>
                    @if($finding->overall_rating)
                    <div class="mt-1" style="color:#F59E0B;font-size:.75rem">
                        @for($i=1;$i<=5;$i++){{ $i <= $finding->overall_rating ? '★' : '☆' }}@endfor
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

</div>
</section>
</x-layouts.app>
```

- [ ] **Step 7.4 — Run all tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=PractitionerDirectoryTest
```
Expected: All 6 tests PASS.

- [ ] **Step 7.5 — Verify no regressions**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```
Expected: All existing tests PASS alongside the new 15+ tests.

- [ ] **Step 7.6 — Cache routes**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan route:cache
```
Expected: `INFO Routes cached successfully.`

- [ ] **Step 7.7 — Commit**

```
git add resources/views/pages/practitioners/ tests/Feature/PractitionerDirectoryTest.php
git commit -m "feat(directory): practitioner profile page with ratings, programs, findings preview"
```

- [ ] **Step 7.8 — Final commit with any remaining changes**

```
git add routes/web.php app/Models/User.php
git commit -m "feat: wire practitioner directory routes + user.practitionerApplications relationship"
```

---

## Self-Review

### Spec coverage check
| Requirement | Task |
|-------------|------|
| Tester dashboard KPI stats | Task 1 |
| Tester profile edit | Task 2 |
| Tester bug reports list | Task 3 |
| Tester nav updated | Task 3.6 |
| Support portal layout | Task 4 |
| Support dashboard with queue | Task 4 |
| Support ticket list + filters | Task 5 |
| Support ticket show with reply | Task 5 |
| Support assign-to-me | Task 5 |
| Practitioner directory listing | Task 6 |
| Directory filters (profession/country) | Task 6 |
| Practitioner profile page | Task 7 |
| No sensitive data exposed in directory | Task 6.1 test |
| Rating breakdown bars | Task 7.3 |
| Programs timeline on profile | Task 7.3 |

### Placeholder scan
No TBDs, TODOs, or incomplete sections found.

### Type consistency check
- `TesterAssignment::statusOptions()` — used in dashboard and bug-reports, matches model definition ✅
- `Ticket` relationships `customer()`, `assignee()`, `publicReplies()`, `testerAssignment()` — all verified from model ✅
- `PractitionerProfile::professionOptions()` — static method exists on model, used in directory ✅
- `PractitionerFinding` fields `overall_rating`, `is_published`, `findings_text` — match model `$fillable` ✅
- `$tier->tailwindBadge()`, `$tier->label()` — methods on `PractitionerTier` enum, already used in existing practitioner dashboard ✅
