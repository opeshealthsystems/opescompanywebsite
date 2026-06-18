# Role-Based Portal Suite — Implementation Spec
_Date: 2026-06-18_

## Goal
Build four complete, mobile-responsive, dark-themed role-based portals for OPES Health Systems:
1. **Practitioner Portal** — enhance existing portal at `/en/practitioner/`
2. **Company Manager Portal** — new portal at `/en/manager/`
3. **Company HR Portal** — new portal at `/en/hr/`
4. **Company Accountant Portal** — new portal at `/en/accountant/`

---

## Tech Stack
- Laravel 13 / PHP 8.3
- Blade templates (no Livewire/Inertia)
- Vite + `app.css` custom CSS classes (`cp-*` / `portal-*`)
- Spatie Laravel Permission (roles & permissions)
- Lucide icons via `vendor/lucide.min.js`
- Dark theme: background `#080F1E`, accent `#00C896` (green), `#1A6FE8` (blue)
- Mobile-first, hamburger nav breakpoint at 768px

---

## Architecture

### New Spatie Roles
| Role | Portal | Middleware guard |
|------|--------|-----------------|
| `manager` | `/en/manager/` | `auth\|role:manager` |
| `hr` | `/en/hr/` | `auth\|role:hr` |
| `accountant` | `/en/accountant/` | `auth\|role:accountant` |

### New Permissions
- `view_manager_dashboard`
- `view_hr_dashboard`
- `view_accountant_dashboard`
- `manage_leave_requests`
- `view_payroll`
- `view_employees`

### Existing Models Used (no new migrations)
- `User` + `EmployeeProfile` — employees
- `Department` — org structure
- `LeaveRequest`, `LeaveBalance` — HR leave management
- `PayrollRun`, `PayrollEntry` — payroll
- `PerformanceReview` — reviews
- `Invoice` — billing
- `Expense` — expenses
- `PractitionerApplication`, `PractitionerFinding`, `PractitionerProgram` — practitioner

### Layout Pattern
Each portal has its own `resources/views/components/layouts/{role}.blade.php` using shared `cp-*` CSS classes. No Tailwind in new layouts. Mobile hamburger nav via `data-portal-burger` attribute + JS toggle.

---

## File Structure

### New Controllers
```
app/Http/Controllers/
├── Manager/
│   ├── DashboardController.php
│   ├── TeamController.php
│   ├── LeaveController.php
│   ├── PerformanceController.php
│   └── ReportController.php
├── HR/
│   ├── DashboardController.php
│   ├── EmployeeController.php
│   ├── LeaveController.php
│   ├── PayrollController.php
│   ├── PerformanceController.php
│   └── DepartmentController.php
└── Accountant/
    ├── DashboardController.php
    ├── InvoiceController.php
    ├── PayrollController.php
    ├── ExpenseController.php
    └── ReportController.php
```

### New Layouts
```
resources/views/components/layouts/
├── manager.blade.php
├── hr.blade.php
└── accountant.blade.php
```

### New Views
```
resources/views/
├── manager/
│   ├── dashboard.blade.php
│   ├── team/index.blade.php
│   ├── leave/index.blade.php
│   ├── performance/index.blade.php
│   └── reports/index.blade.php
├── hr/
│   ├── dashboard.blade.php
│   ├── employees/index.blade.php
│   ├── employees/show.blade.php
│   ├── leave/index.blade.php
│   ├── payroll/index.blade.php
│   ├── payroll/show.blade.php
│   ├── performance/index.blade.php
│   └── departments/index.blade.php
└── accountant/
    ├── dashboard.blade.php
    ├── invoices/index.blade.php
    ├── invoices/show.blade.php
    ├── payroll/index.blade.php
    ├── expenses/index.blade.php
    └── reports/index.blade.php
```

### Enhanced Practitioner Views (existing views updated)
```
resources/views/practitioner/
├── dashboard.blade.php        ← full rebuild
├── profile/show.blade.php     ← add completeness bar
├── applications/index.blade.php ← add payout status column
├── findings/index.blade.php   ← add ratings chart
└── courses/index.blade.php    ← add progress bars
```

---

## Portal Specifications

### 1. Practitioner Portal Enhancements

**Layout** (`practitioner.blade.php`):
- Refactor from Tailwind classes to `cp-*` shared system
- Nav: Dashboard · Profile · Programs · Applications · Findings · Surveys · Suggestions · Courses · Certificates
- Mobile hamburger nav
- Tier badge in header (Bronze/Silver/Gold with colour coding)

**Dashboard** (rich rebuild):
- Tier card with progress to next tier
- Stat row: Active Applications · Submitted Findings · Courses Completed · Avg Rating
- Pending actions banner (surveys due, open applications needing findings)
- Payout tracker table (per-application payout status)
- Recent findings (last 3 with star ratings display)
- Upcoming program deadlines

**Profile**: Add profile completeness progress bar (fields filled / total fields)

**Applications index**: Add payout status badge column (pending / initiated / paid / N/A)

**Findings index**: Show average star rating per finding as visual stars

**Courses index**: Progress bar showing lessons completed / total lessons

---

### 2. Company Manager Portal

**Layout** (`manager.blade.php`):
- Nav: Dashboard · My Team · Leave Requests · Performance · Reports
- Accent colour: `#1A6FE8` (blue)

**Dashboard**:
- Stat cards: My Team Size · Pending Leave · Reviews Due · Open Tickets
- Pending leave requests table (top 5, with Approve/Reject quick actions)
- Team list preview (top 8 employees, name + dept + status)
- Reviews due this month

**My Team** (`/manager/team`):
- Paginated employee list filtered to manager's `department_id`
- Columns: Name, Position, Status, Hire Date, Email, Phone
- Search by name

**Leave Requests** (`/manager/leave`):
- All leave requests from team members
- Filter by status (pending / approved / rejected)
- Approve / Reject with optional note (POST action)

**Performance** (`/manager/performance`):
- List of performance reviews for team
- Status badges (scheduled / completed / overdue)
- Button to initiate new review

**Reports** (`/manager/reports`):
- Leave utilisation table by month
- Attendance summary per employee
- Read-only, printable

---

### 3. Company HR Portal

**Layout** (`hr.blade.php`):
- Nav: Dashboard · Employees · Leave · Payroll · Performance · Departments
- Accent colour: `#8B5CF6` (purple)

**Dashboard**:
- Stat cards: Total Employees · Pending Leave · Active Payroll Run · Contracts Expiring (30d)
- Quick-action buttons (New Leave Approval, View Payroll)
- Contracts expiring alert list
- Recent employee additions (last 5)

**Employees** (`/hr/employees`):
- Full paginated list with search + filter (dept, employment type, status)
- Columns: Name, Email, Dept, Employment Type, Hire Date, Status
- Show page: full profile (EmployeeProfile + salary grade + leave balance)

**Leave** (`/hr/leave`):
- All leave requests, filter by status / dept / date range
- Approve / Reject with note
- Leave balance table per employee on show page

**Payroll** (`/hr/payroll`):
- List of PayrollRun records (period, total employees, total cost, status)
- Show page: all PayrollEntry rows (employee name, gross, deductions, net)

**Performance** (`/hr/performance`):
- All performance reviews across all employees
- Create new review form
- Filter by period / dept / status

**Departments** (`/hr/departments`):
- Department tree (parent → children)
- Headcount per dept
- Department head name
- Edit department head (POST)

---

### 4. Company Accountant Portal

**Layout** (`accountant.blade.php`):
- Nav: Dashboard · Invoices · Payroll Costs · Expenses · Reports
- Accent colour: `#F59E0B` (amber)

**Dashboard**:
- Stat cards: Revenue This Month · Outstanding Total · Overdue Count · Last Payroll Cost · Total Expenses (MTD)
- Trend arrows (vs prior month)
- Overdue invoices alert table (top 5)
- Recent payments (last 5 paid invoices)

**Invoices** (`/accountant/invoices`):
- Full paginated list, filter by status (draft / sent / paid / overdue) + date range
- Columns: Reference, Customer, Amount, Status, Due Date, Issued Date
- Show page: invoice line items, payment history, download link
- Mark as Paid action (POST)

**Payroll Costs** (`/accountant/payroll`):
- PayrollRun list with total cost per run
- Show page: breakdown by department (sum of net salary per dept)
- CSV export button

**Expenses** (`/accountant/expenses`):
- Expense list with category, amount, submitted by, status
- Filter by category / date range
- Approve / Reject actions (if expense model has status)

**Reports** (`/accountant/reports`):
- Monthly P&L summary table (revenue − payroll − expenses = net)
- Outstanding AR (all unpaid invoices grouped by age: 0–30d, 31–60d, 60d+)
- Payroll cost by month chart (CSS bar chart, no JS library)

---

## Routing (routes/web.php additions)

```php
// Manager Portal
Route::prefix('{locale}/manager')->name('manager.')->middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/dashboard',    [Manager\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/team',         [Manager\TeamController::class, 'index'])->name('team');
    Route::get('/leave',        [Manager\LeaveController::class, 'index'])->name('leave.index');
    Route::post('/leave/{id}/approve', [Manager\LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{id}/reject',  [Manager\LeaveController::class, 'reject'])->name('leave.reject');
    Route::get('/performance',  [Manager\PerformanceController::class, 'index'])->name('performance.index');
    Route::post('/performance', [Manager\PerformanceController::class, 'store'])->name('performance.store');
    Route::get('/reports',      [Manager\ReportController::class, 'index'])->name('reports');
});

// HR Portal
Route::prefix('{locale}/hr')->name('hr.')->middleware(['auth', 'role:hr'])->group(function () {
    Route::get('/dashboard',                [HR\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/employees',                [HR\EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/{user}',         [HR\EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/leave',                    [HR\LeaveController::class, 'index'])->name('leave.index');
    Route::post('/leave/{id}/approve',      [HR\LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{id}/reject',       [HR\LeaveController::class, 'reject'])->name('leave.reject');
    Route::get('/payroll',                  [HR\PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/{run}',            [HR\PayrollController::class, 'show'])->name('payroll.show');
    Route::get('/performance',              [HR\PerformanceController::class, 'index'])->name('performance.index');
    Route::post('/performance',             [HR\PerformanceController::class, 'store'])->name('performance.store');
    Route::get('/departments',              [HR\DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments/{dept}/head', [HR\DepartmentController::class, 'updateHead'])->name('departments.head');
});

// Accountant Portal
Route::prefix('{locale}/accountant')->name('accountant.')->middleware(['auth', 'role:accountant'])->group(function () {
    Route::get('/dashboard',           [Accountant\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/invoices',            [Accountant\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}',  [Accountant\InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/invoices/{invoice}/mark-paid', [Accountant\InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
    Route::get('/payroll',             [Accountant\PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/{run}',       [Accountant\PayrollController::class, 'show'])->name('payroll.show');
    Route::get('/expenses',            [Accountant\ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses/{id}/approve', [Accountant\ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('/expenses/{id}/reject',  [Accountant\ExpenseController::class, 'reject'])->name('expenses.reject');
    Route::get('/reports',             [Accountant\ReportController::class, 'index'])->name('reports');
});
```

---

## CSS Strategy

Shared portal CSS classes added to `app.css`:
- `.portal-nav` — top navigation bar (dark, sticky)
- `.portal-burger` — hamburger button (mobile only)
- `.portal-menu` — collapsible nav links container
- `.portal-actions` — user avatar + logout on right
- `.portal-main` — main content area with padding
- `.portal-container` — max-width wrapper
- `.portal-stat-card` — KPI card (icon + number + label)
- `.portal-table` — styled data table
- `.portal-badge` — status pill (colours by class)
- `.portal-section` — page section with heading
- `.portal-flash` — success/error flash banner
- `.portal-form` — form container
- `.portal-progress` — progress bar (for course/profile completeness)

All existing `cp-*` classes remain untouched. New portals use `portal-*` classes.

---

## Constraints
- No new database migrations — all data from existing ERP tables
- Do not modify existing customer/tester/practitioner routes or controllers
- Do not modify Filament resources
- All pages must load without errors even if related tables are empty (use `->count()` not `->sum()` without null guards)
- Locale-aware routes: all portal routes inside `{locale}` prefix
