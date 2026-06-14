# Reports Dashboard — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a read-only Reports Dashboard in the Filament admin panel that shows platform-wide metrics across all major modules: customers, licenses, invoices, tickets, bug reports, and tester assignments.

**Architecture:** A single Filament custom `Page` (not a Resource) gated by `view_reports` permission. The page uses a custom Blade view to render metric cards and tables using the platform's existing CSS conventions — no external chart libraries. All metrics are computed via Eloquent aggregate queries in the page's `mount()` method and passed to the view. No new tables or models needed.

**Existing permission (already seeded):**
- `view_reports` — super_admin, admin

**Tech Stack:** Laravel 13, PHP 8.3, Filament v3.3, Blade

---

## File Map

### New files
- `app/Filament/Pages/ReportsDashboard.php`
- `resources/views/filament/pages/reports-dashboard.blade.php`
- `tests/Feature/ReportsDashboardTest.php`

---

## Task 1: Reports Dashboard Page + View + Tests

**Files:**
- Create: `app/Filament/Pages/ReportsDashboard.php`
- Create: `resources/views/filament/pages/reports-dashboard.blade.php`
- Create: `tests/Feature/ReportsDashboardTest.php`

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/ReportsDashboardTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\License;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ReportsDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_view_reports_permission_exists(): void
    {
        $this->assertDatabaseHas('permissions', ['name' => 'view_reports']);
    }

    public function test_admin_has_view_reports_permission(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->assertTrue($admin->hasPermissionTo('view_reports'));
    }

    public function test_admin_can_access_reports_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/reports-dashboard')
            ->assertOk();
    }

    public function test_customer_cannot_access_reports_dashboard(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)
            ->get('/admin/reports-dashboard')
            ->assertForbidden();
    }

    public function test_reports_dashboard_shows_customer_count(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($admin)
            ->get('/admin/reports-dashboard')
            ->assertOk()
            ->assertSee('Customers');
    }

    public function test_reports_dashboard_shows_ticket_metrics(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        Ticket::create([
            'user_id'     => $customer->id,
            'subject'     => 'Test ticket',
            'description' => 'Test',
            'type'        => 'support',
            'status'      => 'open',
            'priority'    => 'medium',
        ]);

        $this->actingAs($admin)
            ->get('/admin/reports-dashboard')
            ->assertOk()
            ->assertSee('Tickets');
    }
}
```

- [ ] **Step 2: Run tests — expect FAIL (page doesn't exist)**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/ReportsDashboardTest.php
```

Expected: FAIL on HTTP tests (404 — page not registered).

- [ ] **Step 3: Create `app/Filament/Pages/ReportsDashboard.php`**

```php
<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use App\Models\License;
use App\Models\Ticket;
use App\Models\TesterAssignment;
use App\Models\User;
use Filament\Pages\Page;

class ReportsDashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reports';
    protected static ?string $title           = 'Reports Dashboard';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int    $navigationSort  = 50;
    protected static string  $view            = 'filament.pages.reports-dashboard';

    public array $metrics = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo('view_reports') ?? false;
    }

    public function mount(): void
    {
        $this->metrics = $this->buildMetrics();
    }

    protected function buildMetrics(): array
    {
        $now        = now();
        $monthStart = $now->copy()->startOfMonth();

        // Customers
        $totalCustomers    = User::role('customer')->count();
        $newCustomers      = User::role('customer')->where('created_at', '>=', $monthStart)->count();

        // Licenses
        $activeLicenses    = License::where('status', 'active')->count();
        $expiringSoon      = License::where('status', 'active')
            ->where('end_date', '>=', $now)
            ->where('end_date', '<=', $now->copy()->addDays(30))
            ->count();

        // Invoices
        $paidThisMonth     = Invoice::where('status', 'paid')
            ->where('paid_at', '>=', $monthStart)
            ->count();
        $outstandingInvoices = Invoice::whereIn('status', ['sent', 'overdue'])->count();
        $overdueInvoices   = Invoice::where('status', 'overdue')->count();

        // Tickets
        $openTickets       = Ticket::whereIn('status', ['open', 'in_progress', 'pending_customer'])->count();
        $resolvedThisMonth = Ticket::whereIn('status', ['resolved', 'closed'])
            ->where('updated_at', '>=', $monthStart)
            ->count();
        $openBugReports    = Ticket::where('type', 'bug_report')
            ->whereIn('status', ['open', 'in_progress', 'pending_customer'])
            ->count();

        // Tester Assignments
        $pendingAssignments    = TesterAssignment::where('status', 'pending')->count();
        $activeAssignments     = TesterAssignment::where('status', 'in_progress')->count();
        $completedThisMonth   = TesterAssignment::where('status', 'completed')
            ->where('updated_at', '>=', $monthStart)
            ->count();

        // Recent activity
        $recentTickets     = Ticket::with('customer')
            ->whereIn('status', ['open', 'in_progress'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentInvoices    = Invoice::with('customer')
            ->whereIn('status', ['sent', 'overdue'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return compact(
            'totalCustomers', 'newCustomers',
            'activeLicenses', 'expiringSoon',
            'paidThisMonth', 'outstandingInvoices', 'overdueInvoices',
            'openTickets', 'resolvedThisMonth', 'openBugReports',
            'pendingAssignments', 'activeAssignments', 'completedThisMonth',
            'recentTickets', 'recentInvoices'
        );
    }
}
```

- [ ] **Step 4: Create `resources/views/filament/pages/reports-dashboard.blade.php`**

Create directory `resources/views/filament/pages/` if it doesn't exist.

```html
<x-filament-panels::page>
    @php $m = $this->metrics; @endphp

    {{-- Stat cards grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:2rem;">

        {{-- Customers --}}
        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <div style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Customers</div>
            <div style="color:#e2e8f0;font-size:1.875rem;font-weight:700;">{{ $m['totalCustomers'] }}</div>
            <div style="color:#00C896;font-size:0.8125rem;margin-top:0.25rem;">+{{ $m['newCustomers'] }} this month</div>
        </div>

        {{-- Active Licenses --}}
        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <div style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Active Licenses</div>
            <div style="color:#e2e8f0;font-size:1.875rem;font-weight:700;">{{ $m['activeLicenses'] }}</div>
            @if($m['expiringSoon'] > 0)
                <div style="color:#f59e0b;font-size:0.8125rem;margin-top:0.25rem;">{{ $m['expiringSoon'] }} expiring within 30d</div>
            @else
                <div style="color:#64748b;font-size:0.8125rem;margin-top:0.25rem;">None expiring soon</div>
            @endif
        </div>

        {{-- Open Tickets --}}
        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <div style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Tickets</div>
            <div style="color:#e2e8f0;font-size:1.875rem;font-weight:700;">{{ $m['openTickets'] }}</div>
            <div style="color:#64748b;font-size:0.8125rem;margin-top:0.25rem;">{{ $m['resolvedThisMonth'] }} resolved this month</div>
        </div>

        {{-- Bug Reports --}}
        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <div style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Open Bug Reports</div>
            <div style="color:{{ $m['openBugReports'] > 0 ? '#ef4444' : '#e2e8f0' }};font-size:1.875rem;font-weight:700;">{{ $m['openBugReports'] }}</div>
            <div style="color:#64748b;font-size:0.8125rem;margin-top:0.25rem;">From tester reports</div>
        </div>

        {{-- Outstanding Invoices --}}
        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <div style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Outstanding Invoices</div>
            <div style="color:{{ $m['overdueInvoices'] > 0 ? '#ef4444' : '#e2e8f0' }};font-size:1.875rem;font-weight:700;">{{ $m['outstandingInvoices'] }}</div>
            @if($m['overdueInvoices'] > 0)
                <div style="color:#ef4444;font-size:0.8125rem;margin-top:0.25rem;">{{ $m['overdueInvoices'] }} overdue</div>
            @else
                <div style="color:#64748b;font-size:0.8125rem;margin-top:0.25rem;">{{ $m['paidThisMonth'] }} paid this month</div>
            @endif
        </div>

        {{-- Tester Assignments --}}
        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <div style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Tester Assignments</div>
            <div style="color:#e2e8f0;font-size:1.875rem;font-weight:700;">{{ $m['activeAssignments'] }}</div>
            <div style="color:#64748b;font-size:0.8125rem;margin-top:0.25rem;">{{ $m['pendingAssignments'] }} pending &bull; {{ $m['completedThisMonth'] }} done this month</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

        {{-- Recent Open Tickets --}}
        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <h3 style="color:#e2e8f0;font-size:0.875rem;font-weight:600;margin-bottom:1rem;text-transform:uppercase;letter-spacing:0.05em;">Recent Open Tickets</h3>
            @forelse($m['recentTickets'] as $ticket)
            @php
                $priorityColor = match($ticket->priority) {
                    'urgent' => '#ef4444', 'high' => '#f97316',
                    'medium' => '#3b82f6', 'low'  => '#64748b', default => '#94a3b8',
                };
            @endphp
            <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:0.625rem 0;border-bottom:1px solid #0f172a;">
                <div>
                    <p style="color:#e2e8f0;font-size:0.8125rem;font-weight:500;margin:0 0 0.125rem;">{{ Str::limit($ticket->subject, 40) }}</p>
                    <p style="color:#64748b;font-size:0.75rem;margin:0;">{{ $ticket->reference_number }} &bull; {{ $ticket->customer?->name ?? 'Unknown' }}</p>
                </div>
                <span style="color:{{ $priorityColor }};font-size:0.7rem;font-weight:700;text-transform:uppercase;white-space:nowrap;margin-left:0.5rem;">{{ $ticket->priority }}</span>
            </div>
            @empty
            <p style="color:#64748b;font-size:0.875rem;">No open tickets.</p>
            @endforelse
            <div style="margin-top:0.75rem;">
                <a href="{{ \App\Filament\Resources\TicketResource::getUrl('index') }}" style="color:#00C896;font-size:0.75rem;text-decoration:none;">View all tickets →</a>
            </div>
        </div>

        {{-- Outstanding Invoices --}}
        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <h3 style="color:#e2e8f0;font-size:0.875rem;font-weight:600;margin-bottom:1rem;text-transform:uppercase;letter-spacing:0.05em;">Outstanding Invoices</h3>
            @forelse($m['recentInvoices'] as $invoice)
            @php
                $statusColor = match($invoice->status) {
                    'overdue' => '#ef4444', 'sent' => '#3b82f6', default => '#94a3b8',
                };
            @endphp
            <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:0.625rem 0;border-bottom:1px solid #0f172a;">
                <div>
                    <p style="color:#e2e8f0;font-size:0.8125rem;font-weight:500;margin:0 0 0.125rem;">{{ $invoice->invoice_number }}</p>
                    <p style="color:#64748b;font-size:0.75rem;margin:0;">{{ $invoice->customer?->name ?? 'Unknown' }} &bull; Due {{ $invoice->due_date?->format('d M Y') ?? '—' }}</p>
                </div>
                <span style="color:{{ $statusColor }};font-size:0.7rem;font-weight:700;text-transform:uppercase;white-space:nowrap;margin-left:0.5rem;">{{ $invoice->status }}</span>
            </div>
            @empty
            <p style="color:#64748b;font-size:0.875rem;">No outstanding invoices.</p>
            @endforelse
            <div style="margin-top:0.75rem;">
                <a href="{{ \App\Filament\Resources\InvoiceResource::getUrl('index') }}" style="color:#00C896;font-size:0.75rem;text-decoration:none;">View all invoices →</a>
            </div>
        </div>

    </div>
</x-filament-panels::page>
```

- [ ] **Step 5: Run tests — expect all 6 to pass now**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/ReportsDashboardTest.php
```

Expected: 6/6 pass.

- [ ] **Step 6: Run full suite — expect 99 pass, 0 fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 99 tests pass (93 + 6 new), 0 fail.

- [ ] **Step 7: Commit**

```
git add app/Filament/Pages/ReportsDashboard.php resources/views/filament/pages/reports-dashboard.blade.php tests/Feature/ReportsDashboardTest.php
git commit -m "feat: add Filament Reports Dashboard with platform-wide metrics (customers, licenses, tickets, invoices, assignments)"
```

---

## Self-Review

### Spec coverage

| Requirement | Covered |
|---|---|
| Filament custom Page (not Resource) | ✅ `extends Page` |
| Gated by `view_reports` permission | ✅ `canAccess()` |
| Customer metrics (total, new this month) | ✅ `buildMetrics()` |
| License metrics (active, expiring soon) | ✅ |
| Invoice metrics (outstanding, overdue, paid this month) | ✅ |
| Ticket metrics (open, resolved this month, open bug reports) | ✅ |
| Tester assignment metrics (pending, active, completed this month) | ✅ |
| Recent open tickets table | ✅ view |
| Recent outstanding invoices table | ✅ view |
| Navigation: Reporting group, sort 50 | ✅ |
| 6 tests covering permission, access, and content | ✅ |

### Authorization

- `canAccess()` checks `view_reports` permission
- Customers and testers get 403 (they can't access Filament at all via `canAccessPanel()`)
- No mutations on this page — read-only
