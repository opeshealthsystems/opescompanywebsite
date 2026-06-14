# Tester Assignment System — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a tester assignment system — admins create product-test assignments and assign them to internal testers; testers see their workload in a dedicated portal, can start/complete assignments, and file bug reports linked to each assignment.

**Architecture:** One new table (`tester_assignments`). Bug reports reuse the existing `tickets` table (type='bug_report') with a nullable `tester_assignment_id` FK added via a new migration. Testers get their own Blade portal at `/{locale}/tester/...` (same pattern as the customer portal — dark-themed, not Filament). Admins manage assignments from Filament via `TesterAssignmentResource` (gated by `assign_testers` permission). Products are config-driven (same as licenses).

**Existing permissions (already seeded — DO NOT modify RolePermissionSeeder):**
- `assign_testers` — admin, super_admin  
- `view_tester_dashboard` — tester role
- `manage_bug_reports` — admin, support

**Tech Stack:** Laravel 13, PHP 8.3, Filament v3.3, Spatie Laravel Permission, Blade/Tailwind CSS v4, SQLite in-memory tests

---

## File Map

### New files
- `database/migrations/2026_06_14_000000_create_tester_assignments_table.php`
- `database/migrations/2026_06_14_001000_add_tester_assignment_id_to_tickets.php`
- `app/Models/TesterAssignment.php`
- `app/Filament/Resources/TesterAssignmentResource.php`
- `app/Filament/Resources/TesterAssignmentResource/Pages/ListTesterAssignments.php`
- `app/Filament/Resources/TesterAssignmentResource/Pages/CreateTesterAssignment.php`
- `app/Filament/Resources/TesterAssignmentResource/Pages/EditTesterAssignment.php`
- `app/Http/Controllers/Tester/DashboardController.php`
- `app/Http/Controllers/Tester/AssignmentController.php`
- `resources/views/components/layouts/tester.blade.php`
- `resources/views/tester/dashboard.blade.php`
- `resources/views/tester/assignments/index.blade.php`
- `resources/views/tester/assignments/show.blade.php`
- `tests/Feature/TesterAssignmentTest.php`

### Modified files
- `routes/web.php` — add `/{locale}/tester/...` route group
- `app/Http/Middleware/RequireRole.php` — already handles `role:tester` correctly (no change needed, just verify)

---

## Task 1: Migrations + Model + Tests

**Files:**
- Create: `database/migrations/2026_06_14_000000_create_tester_assignments_table.php`
- Create: `database/migrations/2026_06_14_001000_add_tester_assignment_id_to_tickets.php`
- Create: `app/Models/TesterAssignment.php`
- Create: `tests/Feature/TesterAssignmentTest.php`

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/TesterAssignmentTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TesterAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TesterAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_tester_assignments_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('tester_assignments'));
    }

    public function test_tickets_table_has_tester_assignment_id_column(): void
    {
        $this->assertTrue(Schema::hasColumn('tickets', 'tester_assignment_id'));
    }

    public function test_assignment_can_be_created(): void
    {
        $admin  = User::factory()->create();
        $admin->assignRole('admin');
        $tester = User::factory()->create();
        $tester->assignRole('tester');

        $assignment = TesterAssignment::create([
            'assigned_to'  => $tester->id,
            'assigned_by'  => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'title'        => 'Test patient registration flow',
            'description'  => 'Verify that new patients can be registered without errors.',
            'status'       => 'pending',
            'due_date'     => now()->addWeek()->toDateString(),
        ]);

        $this->assertDatabaseHas('tester_assignments', [
            'title'        => 'Test patient registration flow',
            'status'       => 'pending',
            'assigned_to'  => $tester->id,
        ]);

        $this->assertEquals($tester->id, $assignment->tester->id);
        $this->assertEquals($admin->id, $assignment->assigner->id);
    }

    public function test_assign_testers_permission_exists(): void
    {
        $this->assertDatabaseHas('permissions', ['name' => 'assign_testers']);
    }

    public function test_admin_has_assign_testers_permission(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->assertTrue($admin->hasPermissionTo('assign_testers'));
    }

    public function test_tester_has_view_tester_dashboard_permission(): void
    {
        $tester = User::factory()->create();
        $tester->assignRole('tester');
        $this->assertTrue($tester->hasPermissionTo('view_tester_dashboard'));
    }

    public function test_tester_can_view_their_assignments(): void
    {
        $admin  = User::factory()->create();
        $admin->assignRole('admin');
        $tester = User::factory()->create();
        $tester->assignRole('tester');

        TesterAssignment::create([
            'assigned_to'  => $tester->id,
            'assigned_by'  => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'title'        => 'Smoke test login',
            'description'  => 'Test the login flow.',
            'status'       => 'pending',
            'due_date'     => now()->addWeek()->toDateString(),
        ]);

        $this->actingAs($tester)
            ->get('/en/tester/assignments')
            ->assertOk()
            ->assertSee('Smoke test login');
    }

    public function test_tester_cannot_view_another_testers_assignment(): void
    {
        $admin   = User::factory()->create();
        $admin->assignRole('admin');
        $tester1 = User::factory()->create();
        $tester1->assignRole('tester');
        $tester2 = User::factory()->create();
        $tester2->assignRole('tester');

        $assignment = TesterAssignment::create([
            'assigned_to'  => $tester1->id,
            'assigned_by'  => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'title'        => 'Private test',
            'description'  => 'Only tester1 should see this.',
            'status'       => 'pending',
            'due_date'     => now()->addWeek()->toDateString(),
        ]);

        $this->actingAs($tester2)
            ->get('/en/tester/assignments/' . $assignment->id)
            ->assertForbidden();
    }

    public function test_tester_can_update_assignment_status(): void
    {
        $admin  = User::factory()->create();
        $admin->assignRole('admin');
        $tester = User::factory()->create();
        $tester->assignRole('tester');

        $assignment = TesterAssignment::create([
            'assigned_to'  => $tester->id,
            'assigned_by'  => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'title'        => 'Test flow',
            'description'  => 'Test it.',
            'status'       => 'pending',
            'due_date'     => now()->addWeek()->toDateString(),
        ]);

        $this->actingAs($tester)
            ->patch('/en/tester/assignments/' . $assignment->id . '/status', ['status' => 'in_progress'])
            ->assertRedirect();

        $this->assertDatabaseHas('tester_assignments', [
            'id'     => $assignment->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_bug_report_can_be_filed_linked_to_assignment(): void
    {
        $admin  = User::factory()->create();
        $admin->assignRole('admin');
        $tester = User::factory()->create();
        $tester->assignRole('tester');

        $assignment = TesterAssignment::create([
            'assigned_to'  => $tester->id,
            'assigned_by'  => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'title'        => 'Test patient flow',
            'description'  => 'Test.',
            'status'       => 'in_progress',
            'due_date'     => now()->addWeek()->toDateString(),
        ]);

        $this->actingAs($tester)
            ->post('/en/tester/assignments/' . $assignment->id . '/bug-reports', [
                'subject'     => 'Registration fails with special characters',
                'description' => 'Steps to reproduce: enter special chars in name field.',
                'priority'    => 'high',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'type'                  => 'bug_report',
            'tester_assignment_id'  => $assignment->id,
            'user_id'               => $tester->id,
            'priority'              => 'high',
        ]);
    }
}
```

- [ ] **Step 2: Run tests — expect FAIL (tables don't exist)**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/TesterAssignmentTest.php
```

Expected: FAIL on table existence. HTTP tests will also fail until Task 3.

- [ ] **Step 3: Create `database/migrations/2026_06_14_000000_create_tester_assignments_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tester_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('product_slug');
            $table->string('product_name');
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tester_assignments');
    }
};
```

- [ ] **Step 4: Create `database/migrations/2026_06_14_001000_add_tester_assignment_id_to_tickets.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('tester_assignment_id')
                ->nullable()
                ->after('closed_at')
                ->constrained('tester_assignments')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['tester_assignment_id']);
            $table->dropColumn('tester_assignment_id');
        });
    }
};
```

- [ ] **Step 5: Create `app/Models/TesterAssignment.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TesterAssignment extends Model
{
    protected $fillable = [
        'assigned_to', 'assigned_by', 'product_slug', 'product_name',
        'title', 'description', 'status', 'due_date', 'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function tester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function bugReports(): HasMany
    {
        return $this->hasMany(Ticket::class, 'tester_assignment_id');
    }

    public static function statusOptions(): array
    {
        return [
            'pending'     => 'Pending',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled',
        ];
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'in_progress']);
    }

    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isPast()
            && $this->isActive();
    }
}
```

- [ ] **Step 6: Update `Ticket` model `$fillable` to include `tester_assignment_id`**

Read `app/Models/Ticket.php`. Add `'tester_assignment_id'` to the `$fillable` array. Also add a `testerAssignment()` BelongsTo relationship:

```php
public function testerAssignment(): BelongsTo
{
    return $this->belongsTo(TesterAssignment::class);
}
```

Add `use App\Models\TesterAssignment;` import if needed (or use the model class directly in the relationship).

- [ ] **Step 7: Run migrations**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan migrate
```

- [ ] **Step 8: Run model tests (non-HTTP) — expect PASS**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/TesterAssignmentTest.php --filter="test_tester_assignments_table_exists|test_tickets_table_has_tester_assignment_id_column|test_assignment_can_be_created|test_assign_testers_permission_exists|test_admin_has_assign_testers_permission|test_tester_has_view_tester_dashboard_permission"
```

Expected: 6 tests pass. HTTP tests will fail until Task 3.

- [ ] **Step 9: Run full suite**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 79 pass (73 existing + 6 new), 4 fail (HTTP tester tests pending Task 3).

- [ ] **Step 10: Commit**

```
git add database/migrations/2026_06_14_000000_create_tester_assignments_table.php database/migrations/2026_06_14_001000_add_tester_assignment_id_to_tickets.php app/Models/TesterAssignment.php app/Models/Ticket.php tests/Feature/TesterAssignmentTest.php
git commit -m "feat: add tester_assignments table, TesterAssignment model, and assignment tests"
```

---

## Task 2: Filament TesterAssignmentResource (Admin Panel)

**Files:**
- Create: `app/Filament/Resources/TesterAssignmentResource.php`
- Create: `app/Filament/Resources/TesterAssignmentResource/Pages/ListTesterAssignments.php`
- Create: `app/Filament/Resources/TesterAssignmentResource/Pages/CreateTesterAssignment.php`
- Create: `app/Filament/Resources/TesterAssignmentResource/Pages/EditTesterAssignment.php`

- [ ] **Step 1: Create `app/Filament/Resources/TesterAssignmentResource.php`**

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TesterAssignmentResource\Pages;
use App\Models\TesterAssignment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TesterAssignmentResource extends Resource
{
    protected static ?string $model = TesterAssignment::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Support';
    protected static ?int $navigationSort = 35;
    protected static ?string $label = 'Tester Assignment';
    protected static ?string $pluralLabel = 'Tester Assignments';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo('assign_testers') ?? false;
    }

    public static function getProductOptions(): array
    {
        $options = [];
        foreach (config('products', []) as $product) {
            $slug = $product['slug'] ?? null;
            $name = $product['name'] ?? null;
            if ($slug && $name) {
                $options[$slug] = $name;
            }
        }
        foreach (config('products_specialist', []) as $product) {
            $slug = $product['slug'] ?? null;
            $name = $product['name'] ?? null;
            if ($slug && $name) {
                $options[$slug] = $name;
            }
        }
        return $options;
    }

    public static function form(Form $form): Form
    {
        $productOptions = static::getProductOptions();

        return $form->schema([
            Forms\Components\Section::make('Assignment Details')->schema([
                Forms\Components\Select::make('assigned_to')
                    ->label('Tester')
                    ->options(fn () => User::role('tester')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('product_slug')
                    ->label('Product')
                    ->options($productOptions)
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) use ($productOptions) {
                        if ($state && isset($productOptions[$state])) {
                            $set('product_name', $productOptions[$state]);
                        }
                    }),

                Forms\Components\Hidden::make('product_name'),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Select::make('status')
                    ->options(TesterAssignment::statusOptions())
                    ->default('pending')
                    ->required(),

                Forms\Components\DatePicker::make('due_date')
                    ->nullable(),

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->nullable()
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tester.name')
                    ->label('Tester')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product')
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'     => 'gray',
                        'in_progress' => 'warning',
                        'completed'   => 'success',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Assigned')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(TesterAssignment::statusOptions()),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Tester')
                    ->options(fn () => User::role('tester')->orderBy('name')->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTesterAssignments::route('/'),
            'create' => Pages\CreateTesterAssignment::route('/create'),
            'edit'   => Pages\EditTesterAssignment::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 2: Create `app/Filament/Resources/TesterAssignmentResource/Pages/ListTesterAssignments.php`**

```php
<?php

namespace App\Filament\Resources\TesterAssignmentResource\Pages;

use App\Filament\Resources\TesterAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTesterAssignments extends ListRecords
{
    protected static string $resource = TesterAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
```

- [ ] **Step 3: Create `app/Filament/Resources/TesterAssignmentResource/Pages/CreateTesterAssignment.php`**

```php
<?php

namespace App\Filament\Resources\TesterAssignmentResource\Pages;

use App\Filament\Resources\TesterAssignmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTesterAssignment extends CreateRecord
{
    protected static string $resource = TesterAssignmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['assigned_by'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

- [ ] **Step 4: Create `app/Filament/Resources/TesterAssignmentResource/Pages/EditTesterAssignment.php`**

```php
<?php

namespace App\Filament\Resources\TesterAssignmentResource\Pages;

use App\Filament\Resources\TesterAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTesterAssignment extends EditRecord
{
    protected static string $resource = TesterAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

- [ ] **Step 5: Run full test suite — expect 79 pass, 4 fail (same HTTP tests)**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

- [ ] **Step 6: Commit**

```
git add app/Filament/Resources/TesterAssignmentResource.php app/Filament/Resources/TesterAssignmentResource/
git commit -m "feat: add Filament TesterAssignmentResource for admin assignment management"
```

---

## Task 3: Tester Portal

**Files:**
- Create: `app/Http/Controllers/Tester/DashboardController.php`
- Create: `app/Http/Controllers/Tester/AssignmentController.php`
- Create: `resources/views/components/layouts/tester.blade.php`
- Create: `resources/views/tester/dashboard.blade.php`
- Create: `resources/views/tester/assignments/index.blade.php`
- Create: `resources/views/tester/assignments/show.blade.php`
- Modify: `routes/web.php` — add `/{locale}/tester/...` group

- [ ] **Step 1: Create `app/Http/Controllers/Tester/DashboardController.php`**

```php
<?php

namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use App\Models\TesterAssignment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $active    = TesterAssignment::where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date')
            ->get();

        $completed = TesterAssignment::where('assigned_to', $user->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        return view('tester.dashboard', compact('active', 'completed'));
    }
}
```

- [ ] **Step 2: Create `app/Http/Controllers/Tester/AssignmentController.php`**

```php
<?php

namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TesterAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        $assignments = TesterAssignment::where('assigned_to', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('tester.assignments.index', compact('assignments'));
    }

    public function show(Request $request)
    {
        $user       = Auth::user();
        $id         = (int) $request->route('id');
        $assignment = TesterAssignment::with('bugReports')->findOrFail($id);

        abort_if((int) $assignment->assigned_to !== $user->id, 403);

        return view('tester.assignments.show', compact('assignment'));
    }

    public function updateStatus(Request $request)
    {
        $user       = Auth::user();
        $id         = (int) $request->route('id');
        $assignment = TesterAssignment::findOrFail($id);

        abort_if((int) $assignment->assigned_to !== $user->id, 403);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $assignment->update(['status' => $validated['status']]);

        return redirect()
            ->route('tester.assignments.show', ['locale' => app()->getLocale(), 'id' => $assignment->id])
            ->with('success', 'Assignment status updated.');
    }

    public function storeBugReport(Request $request)
    {
        $user       = Auth::user();
        $id         = (int) $request->route('id');
        $assignment = TesterAssignment::findOrFail($id);

        abort_if((int) $assignment->assigned_to !== $user->id, 403);
        abort_unless($assignment->isActive(), 403, 'Cannot file a bug report on a completed or cancelled assignment.');

        $validated = $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'priority'    => 'required|in:low,medium,high,urgent',
        ]);

        Ticket::create(array_merge($validated, [
            'user_id'              => $user->id,
            'type'                 => 'bug_report',
            'status'               => 'open',
            'tester_assignment_id' => $assignment->id,
        ]));

        return redirect()
            ->route('tester.assignments.show', ['locale' => app()->getLocale(), 'id' => $assignment->id])
            ->with('success', 'Bug report filed and sent to support.');
    }
}
```

- [ ] **Step 3: Add tester routes to `routes/web.php`**

Inside the `Route::prefix('{locale}')...->group(...)` block, after the customer group, add:

```php
        // Tester portal (auth + tester role required)
        Route::middleware(['auth', 'role:tester'])
            ->prefix('tester')
            ->name('tester.')
            ->group(function () {
                Route::get('/dashboard',                                         [\App\Http\Controllers\Tester\DashboardController::class,    'index'])->name('dashboard');
                Route::get('/assignments',                                       [\App\Http\Controllers\Tester\AssignmentController::class,   'index'])->name('assignments');
                Route::get('/assignments/{id}',                                  [\App\Http\Controllers\Tester\AssignmentController::class,   'show'])->name('assignments.show');
                Route::patch('/assignments/{id}/status',                         [\App\Http\Controllers\Tester\AssignmentController::class,   'updateStatus'])->name('assignments.status');
                Route::post('/assignments/{id}/bug-reports',                     [\App\Http\Controllers\Tester\AssignmentController::class,   'storeBugReport'])->name('assignments.bug-reports');
            });
```

- [ ] **Step 4: Create `resources/views/components/layouts/tester.blade.php`**

```html
@props(['title' => 'Tester Portal'])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — OPES Tester Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cp-body">
    <nav class="cp-nav">
        <a href="{{ route('tester.dashboard', ['locale' => app()->getLocale()]) }}" class="cp-nav-brand">
            <span class="cp-brand-opes">OPES</span>
            <span class="cp-brand-name"> Tester</span>
        </a>
        <div class="cp-nav-links">
            <a href="{{ route('tester.dashboard', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('tester.dashboard') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="layout-dashboard" style="width:16px;height:16px"></i> Dashboard
            </a>
            <a href="{{ route('tester.assignments', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('tester.assignments*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="clipboard-list" style="width:16px;height:16px"></i> Assignments
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
    </nav>

    @if (session('success'))
        <div class="cp-flash-success">{{ session('success') }}</div>
    @endif

    <main class="cp-main">
        <div class="cp-container">
            {{ $slot }}
        </div>
    </main>

    <script src="https://unpkg.com/lucide@0.511.0/dist/umd/lucide.min.js" crossorigin="anonymous"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
```

- [ ] **Step 5: Create `resources/views/tester/dashboard.blade.php`**

```html
<x-layouts.tester title="Tester Dashboard">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">Welcome, {{ auth()->user()->name }}</h1>
            <p class="cp-page-subtitle">Your active testing assignments</p>
        </div>
    </div>

    @if($active->isEmpty())
        <div class="cp-section-card" style="text-align:center;padding:3rem;">
            <div class="cp-empty-state">
                <i data-lucide="beaker" style="width:48px;height:48px;color:#334155"></i>
                <p>No active assignments.</p>
                <p style="font-size:0.8125rem">New testing assignments will appear here when assigned by admin.</p>
            </div>
        </div>
    @else
        <div style="display:grid;gap:1rem;">
            @foreach($active as $assignment)
            @php
                $statusColor = match($assignment->status) {
                    'pending'     => '#94a3b8',
                    'in_progress' => '#eab308',
                    default       => '#64748b',
                };
                $overdue = $assignment->isOverdue();
            @endphp
            <div class="cp-section-card" style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;">
                <div style="flex:1;">
                    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;">
                        <span style="color:{{ $statusColor }};font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">
                            {{ \App\Models\TesterAssignment::statusOptions()[$assignment->status] ?? $assignment->status }}
                        </span>
                        @if($overdue)
                            <span style="color:#ef4444;font-size:0.7rem;font-weight:600;">&#9888; OVERDUE</span>
                        @endif
                    </div>
                    <p style="color:#e2e8f0;font-weight:600;font-size:0.9375rem;margin-bottom:0.25rem;">{{ $assignment->title }}</p>
                    <p style="color:#64748b;font-size:0.8125rem;">{{ $assignment->product_name }}</p>
                    @if($assignment->due_date)
                        <p style="color:{{ $overdue ? '#ef4444' : '#64748b' }};font-size:0.75rem;margin-top:0.25rem;">
                            Due: {{ $assignment->due_date->format('d M Y') }}
                        </p>
                    @endif
                </div>
                <a href="{{ route('tester.assignments.show', ['locale' => app()->getLocale(), 'id' => $assignment->id]) }}"
                   class="cp-btn-outline" style="white-space:nowrap;font-size:0.75rem;">View</a>
            </div>
            @endforeach
        </div>
    @endif

    @if($completed->isNotEmpty())
        <div style="margin-top:2rem;">
            <h2 style="color:#64748b;font-size:0.875rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:1rem;">Recently Completed</h2>
            <div class="cp-section-card" style="padding:0;">
                @foreach($completed as $assignment)
                <div style="padding:0.75rem 1rem;border-bottom:1px solid #1e293b;display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <span style="color:#64748b;font-size:0.875rem;">{{ $assignment->title }}</span>
                        <span style="color:#334155;font-size:0.75rem;margin-left:0.5rem;">{{ $assignment->product_name }}</span>
                    </div>
                    <span style="color:{{ $assignment->status === 'completed' ? '#00C896' : '#64748b' }};font-size:0.75rem;font-weight:600;text-transform:capitalize;">
                        {{ $assignment->status }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.tester>
```

- [ ] **Step 6: Create `resources/views/tester/assignments/index.blade.php`**

Create directory `resources/views/tester/assignments/` first.

```html
<x-layouts.tester title="My Assignments">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">My Assignments</h1>
            <p class="cp-page-subtitle">All testing assignments assigned to you</p>
        </div>
    </div>

    @if($assignments->isEmpty())
        <div class="cp-section-card" style="text-align:center;padding:3rem;">
            <div class="cp-empty-state">
                <i data-lucide="clipboard-list" style="width:48px;height:48px;color:#334155"></i>
                <p>No assignments yet.</p>
            </div>
        </div>
    @else
        <div class="cp-section-card" style="padding:0;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #334155;">
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Title</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Product</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Due Date</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $assignment)
                    @php
                        $statusColor = match($assignment->status) {
                            'pending'     => '#94a3b8',
                            'in_progress' => '#eab308',
                            'completed'   => '#00C896',
                            'cancelled'   => '#64748b',
                            default       => '#94a3b8',
                        };
                        $overdue = $assignment->isOverdue();
                    @endphp
                    <tr style="border-bottom:1px solid #1e293b;">
                        <td style="padding:0.75rem;color:#e2e8f0;font-size:0.875rem;">{{ Str::limit($assignment->title, 45) }}</td>
                        <td style="padding:0.75rem;color:#94a3b8;font-size:0.875rem;">{{ $assignment->product_name }}</td>
                        <td style="padding:0.75rem;">
                            <span style="color:{{ $statusColor }};font-size:0.8125rem;font-weight:600;">
                                {{ \App\Models\TesterAssignment::statusOptions()[$assignment->status] ?? $assignment->status }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;color:{{ $overdue ? '#ef4444' : '#64748b' }};font-size:0.8125rem;">
                            {{ $assignment->due_date?->format('d M Y') ?? '—' }}
                            @if($overdue) <span style="font-size:0.7rem;">&#9888;</span> @endif
                        </td>
                        <td style="padding:0.75rem;text-align:right;">
                            <a href="{{ route('tester.assignments.show', ['locale' => app()->getLocale(), 'id' => $assignment->id]) }}"
                               class="cp-btn-outline" style="font-size:0.75rem;padding:0.375rem 0.75rem;">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:1rem 0.75rem 0;">
                {{ $assignments->links() }}
            </div>
        </div>
    @endif
</x-layouts.tester>
```

- [ ] **Step 7: Create `resources/views/tester/assignments/show.blade.php`**

```html
<x-layouts.tester title="{{ $assignment->title }}">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">{{ $assignment->title }}</h1>
            <p class="cp-page-subtitle">{{ $assignment->product_name }}</p>
        </div>
        <a href="{{ route('tester.assignments', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">&larr; Back</a>
    </div>

    @php
        $statusColor = match($assignment->status) {
            'pending'     => '#94a3b8',
            'in_progress' => '#eab308',
            'completed'   => '#00C896',
            'cancelled'   => '#64748b',
            default       => '#94a3b8',
        };
        $isActive = $assignment->isActive();
        $overdue  = $assignment->isOverdue();
    @endphp

    @if($overdue)
        <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;">
            <p style="color:#ef4444;font-weight:600;font-size:0.875rem;margin:0;">&#9888; This assignment is overdue</p>
            <p style="color:#64748b;font-size:0.8rem;margin:0.25rem 0 0;">Due date was {{ $assignment->due_date->format('d M Y') }}. Please update the status or contact your admin.</p>
        </div>
    @endif

    <div class="cp-section-card" style="margin-bottom:1rem;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;">
            <span style="color:{{ $statusColor }};font-weight:700;font-size:0.875rem;text-transform:uppercase;letter-spacing:0.05em;">
                {{ \App\Models\TesterAssignment::statusOptions()[$assignment->status] ?? $assignment->status }}
            </span>
            @if($assignment->due_date)
                <span style="color:{{ $overdue ? '#ef4444' : '#64748b' }};font-size:0.8125rem;">
                    Due: {{ $assignment->due_date->format('d M Y') }}
                </span>
            @endif
        </div>

        <div style="margin-bottom:1.25rem;">
            <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">What to test</p>
            <p style="color:#e2e8f0;font-size:0.875rem;line-height:1.7;white-space:pre-wrap;">{{ $assignment->description }}</p>
        </div>

        @if($assignment->notes)
        <div style="border-top:1px solid #334155;padding-top:1rem;">
            <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Admin Notes</p>
            <p style="color:#94a3b8;font-size:0.875rem;line-height:1.6;">{{ $assignment->notes }}</p>
        </div>
        @endif
    </div>

    @if($isActive)
    <div class="cp-section-card" style="margin-bottom:1rem;">
        <h3 style="color:#e2e8f0;font-size:0.9rem;font-weight:600;margin-bottom:1rem;">Update Status</h3>
        <form method="POST" action="{{ route('tester.assignments.status', ['locale' => app()->getLocale(), 'id' => $assignment->id]) }}" style="display:flex;gap:0.75rem;flex-wrap:wrap;">
            @csrf
            @method('PATCH')
            @if($assignment->status === 'pending')
                <button type="submit" name="status" value="in_progress" class="cp-btn-primary">Start Testing</button>
            @elseif($assignment->status === 'in_progress')
                <button type="submit" name="status" value="completed" class="cp-btn-primary">Mark Complete</button>
            @endif
            <button type="submit" name="status" value="cancelled" class="cp-btn-outline" style="color:#ef4444;border-color:#ef4444;"
                onclick="return confirm('Cancel this assignment?')">Cancel Assignment</button>
        </form>
    </div>

    <div class="cp-section-card">
        <h3 style="color:#e2e8f0;font-size:0.9rem;font-weight:600;margin-bottom:1rem;">File a Bug Report</h3>
        <form method="POST" action="{{ route('tester.assignments.bug-reports', ['locale' => app()->getLocale(), 'id' => $assignment->id]) }}">
            @csrf

            <div style="margin-bottom:1rem;">
                <label style="display:block;color:#94a3b8;font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Bug Title *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                    style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;"
                    placeholder="Brief description of the bug">
                @error('subject') <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block;color:#94a3b8;font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Priority *</label>
                <select name="priority" required
                    style="width:200px;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;">
                    @foreach(\App\Models\Ticket::priorityOptions() as $value => $label)
                        <option value="{{ $value }}" {{ old('priority', 'medium') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('priority') <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom:1.25rem;">
                <label style="display:block;color:#94a3b8;font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Steps to Reproduce / Description *</label>
                <textarea name="description" required maxlength="10000" rows="6"
                    style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;resize:vertical;"
                    placeholder="Describe the bug and steps to reproduce it...">{{ old('description') }}</textarea>
                @error('description') <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="cp-btn-primary">Submit Bug Report</button>
        </form>
    </div>
    @endif

    @if($assignment->bugReports->isNotEmpty())
    <div style="margin-top:1.5rem;">
        <h2 style="color:#94a3b8;font-size:0.875rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:1rem;">
            Bug Reports Filed ({{ $assignment->bugReports->count() }})
        </h2>
        <div class="cp-section-card" style="padding:0;">
            @foreach($assignment->bugReports as $bug)
            @php
                $priorityColor = match($bug->priority) {
                    'urgent' => '#ef4444', 'high' => '#f97316',
                    'medium' => '#3b82f6', 'low'  => '#64748b', default => '#94a3b8',
                };
            @endphp
            <div style="padding:0.875rem 1rem;border-bottom:1px solid #1e293b;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                    <span style="color:#e2e8f0;font-size:0.875rem;font-weight:500;">{{ $bug->subject }}</span>
                    <div style="display:flex;gap:0.5rem;align-items:center;">
                        <span style="color:{{ $priorityColor }};font-size:0.75rem;font-weight:600;text-transform:capitalize;">{{ $bug->priority }}</span>
                        <span style="color:#64748b;font-size:0.75rem;">{{ $bug->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</x-layouts.tester>
```

- [ ] **Step 8: Run all tests — all 83 should pass**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 83 tests pass (73 + 10 new), 0 fail.

- [ ] **Step 9: Commit**

```
git add app/Http/Controllers/Tester/ resources/views/components/layouts/tester.blade.php resources/views/tester/ routes/web.php
git commit -m "feat: add tester portal with assignments list, detail view, status update, and bug report filing"
```

---

## Self-Review

### 1. Spec coverage

| Requirement | Covered |
|---|---|
| `tester_assignments` table | ✅ Task 1 migration |
| `tester_assignment_id` FK added to `tickets` | ✅ Task 1 migration |
| `TesterAssignment` model with relationships + helpers | ✅ Task 1 |
| `Ticket::testerAssignment()` relationship + fillable | ✅ Task 1 |
| Filament admin resource (assign_testers permission) | ✅ Task 2 |
| Product dropdown from config | ✅ Task 2 |
| `assigned_by` stamped on create | ✅ Task 2 `mutateFormDataBeforeCreate` |
| Tester portal layout | ✅ Task 3 |
| Tester dashboard with active/completed split | ✅ Task 3 |
| Tester assignments list + show | ✅ Task 3 |
| Status update (pending→in_progress→completed) | ✅ Task 3 |
| Bug report filing creates Ticket type='bug_report' | ✅ Task 3 |
| Bug reports linked to assignment via FK | ✅ Task 3 |
| Tester isolation (abort_if user mismatch) | ✅ Task 3 |
| `view_tester_dashboard` permission tested | ✅ Task 1 test |
| `assign_testers` permission tested | ✅ Task 1 test |

### 2. Route ordering

No create/show route conflicts — tester routes are `/{id}` only (no `/create` literal that would clash). PATCH routes use method override (PATCH form + `@method('PATCH')`).

### 3. Authorization

- Tester portal: `['auth', 'role:tester']` middleware + `abort_if` ownership check on show/update/bug-report
- Admin resource: `canAccess()` checks `assign_testers` permission (not just role)
- Customers and testers are in completely separate route groups
