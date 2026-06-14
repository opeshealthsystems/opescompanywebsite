# RBAC & User/Employee Foundation — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Install Spatie Laravel Permission, enhance the `users` table with employee fields, define five roles (`super_admin`, `admin`, `support`, `tester`, `customer`), seed 14 permissions, add role-based route middleware, restrict Filament to staff roles, and surface User + Role management in the Filament admin panel.

**Architecture:** Spatie Laravel Permission stores roles/permissions in five pivot tables alongside the existing `users` table. Employee fields (employee_id, department, position, hire_date, phone, is_active, avatar) are added directly to `users` — no separate employees table needed at this stage. Filament resources let super_admin users manage users and view roles. A `RequireRole` middleware guards future customer-portal and tester routes. The existing admin user is upgraded to `super_admin` by the seeder.

**Tech Stack:** Laravel 13, PHP 8.3, Spatie Laravel Permission 6.x, Filament 3.3, MySQL 8, PHPUnit 11, SQLite (tests)

---

## File Map

### New files
- `database/migrations/2026_06_13_200000_add_employee_fields_to_users_table.php`
- `database/seeders/RolePermissionSeeder.php`
- `app/Http/Middleware/RequireRole.php`
- `app/Filament/Resources/UserResource.php`
- `app/Filament/Resources/UserResource/Pages/ListUsers.php`
- `app/Filament/Resources/UserResource/Pages/CreateUser.php`
- `app/Filament/Resources/UserResource/Pages/EditUser.php`
- `app/Filament/Resources/RoleResource.php`
- `app/Filament/Resources/RoleResource/Pages/ListRoles.php`
- `app/Filament/Resources/RoleResource/Pages/ViewRole.php`
- `tests/Feature/RbacTest.php`

### Modified files
- `app/Models/User.php` — add HasRoles trait, switch to `$fillable` array, add employee fields, restrict `canAccessPanel`
- `database/seeders/DatabaseSeeder.php` — call `RolePermissionSeeder` after `AdminUserSeeder`
- `bootstrap/app.php` — register `role` middleware alias

---

## Task 1: Install Spatie Laravel Permission

**Files:**
- Modify: `composer.json` (via composer)
- Create: `config/permission.php` (via publish)
- Create: `database/migrations/YYYY_..._create_permission_tables.php` (via publish)

- [ ] **Step 1: Require the package**

```bash
cd C:/laragon/www/ohs
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe -r "passthru('composer require spatie/laravel-permission');"
```

Or run in Bash:
```bash
cd /c/laragon/www/ohs && composer require spatie/laravel-permission
```

Expected: `Package operations: 1 install, ...` with `spatie/laravel-permission` listed.

- [ ] **Step 2: Publish the config and migration**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Expected output:
```
Copied File [...] to [config/permission.php]
Copied File [...] to [database/migrations/..._create_permission_tables.php]
```

- [ ] **Step 3: Verify `config/permission.php` — disable Teams feature**

Open `config/permission.php` and confirm `'teams' => false` (it is false by default). No change needed unless Teams is enabled.

- [ ] **Step 4: Run the permission migrations**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan migrate
```

Expected: Five new tables migrated — `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`.

- [ ] **Step 5: Write a smoke test**

Add to `tests/Feature/RbacTest.php`:

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('roles'));
        $this->assertTrue(Schema::hasTable('permissions'));
        $this->assertTrue(Schema::hasTable('model_has_roles'));
        $this->assertTrue(Schema::hasTable('model_has_permissions'));
        $this->assertTrue(Schema::hasTable('role_has_permissions'));
    }
}
```

- [ ] **Step 6: Run the test to verify it passes**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/RbacTest.php --filter=test_permission_tables_exist
```

Expected: `PASS`

- [ ] **Step 7: Commit**

```bash
git add composer.json composer.lock config/permission.php database/migrations/
git commit -m "feat: install spatie/laravel-permission"
```

---

## Task 2: Add Employee Fields to Users Table

**Files:**
- Create: `database/migrations/2026_06_13_200000_add_employee_fields_to_users_table.php`
- Modify: `app/Models/User.php` (fillable + new fields)

- [ ] **Step 1: Write the failing test (new user fields)**

Add to `tests/Feature/RbacTest.php`:

```php
public function test_user_has_employee_fields(): void
{
    $user = \App\Models\User::factory()->create([
        'employee_id' => 'EMP-2026-0001',
        'department'  => 'Engineering',
        'position'    => 'Software Developer',
        'phone'       => '+237612345678',
        'is_active'   => true,
    ]);

    $this->assertDatabaseHas('users', [
        'employee_id' => 'EMP-2026-0001',
        'department'  => 'Engineering',
        'position'    => 'Software Developer',
    ]);
}
```

- [ ] **Step 2: Run the test — expect it to FAIL**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/RbacTest.php --filter=test_user_has_employee_fields
```

Expected: FAIL — `Unknown column 'employee_id'`

- [ ] **Step 3: Create the migration**

Create `database/migrations/2026_06_13_200000_add_employee_fields_to_users_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id')->nullable()->unique()->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->string('department')->nullable()->after('phone');
            $table->string('position')->nullable()->after('department');
            $table->date('hire_date')->nullable()->after('position');
            $table->boolean('is_active')->default(true)->after('hire_date');
            $table->string('avatar')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_id', 'phone', 'department',
                'position', 'hire_date', 'is_active', 'avatar',
            ]);
        });
    }
};
```

- [ ] **Step 4: Update `app/Models/User.php` — switch to `$fillable` array, add new fields**

Replace the entire file content:

```php
<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'phone',
        'department',
        'position',
        'hire_date',
        'is_active',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin', 'support']);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'hire_date'         => 'date',
            'is_active'         => 'boolean',
        ];
    }
}
```

- [ ] **Step 5: Run the migration**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan migrate
```

Expected: `Migrated: 2026_06_13_200000_add_employee_fields_to_users_table`

- [ ] **Step 6: Run the test — expect it to PASS**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/RbacTest.php --filter=test_user_has_employee_fields
```

Expected: `PASS`

- [ ] **Step 7: Run full test suite to ensure no regressions**

```bash
C:/laragon\bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test
```

Expected: All previously passing tests still pass.

- [ ] **Step 8: Commit**

```bash
git add database/migrations/2026_06_13_200000_add_employee_fields_to_users_table.php app/Models/User.php tests/Feature/RbacTest.php
git commit -m "feat: add employee fields to users table and HasRoles to User model"
```

---

## Task 3: Seed Roles and Permissions

**Files:**
- Create: `database/seeders/RolePermissionSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Write failing tests for roles**

Add to `tests/Feature/RbacTest.php`:

```php
public function test_five_roles_exist_after_seeding(): void
{
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    foreach (['super_admin', 'admin', 'support', 'tester', 'customer'] as $role) {
        $this->assertDatabaseHas('roles', ['name' => $role]);
    }
}

public function test_super_admin_has_all_permissions(): void
{
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    $superAdmin = \Spatie\Permission\Models\Role::findByName('super_admin');
    $this->assertGreaterThan(0, $superAdmin->permissions->count());
    $this->assertTrue($superAdmin->hasPermissionTo('manage_roles'));
    $this->assertTrue($superAdmin->hasPermissionTo('manage_accounting'));
}

public function test_customer_role_has_no_permissions(): void
{
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    $customer = \Spatie\Permission\Models\Role::findByName('customer');
    $this->assertEquals(0, $customer->permissions->count());
}

public function test_support_role_cannot_manage_accounting(): void
{
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    $support = \Spatie\Permission\Models\Role::findByName('support');
    $this->assertFalse($support->hasPermissionTo('manage_accounting'));
    $this->assertTrue($support->hasPermissionTo('manage_tickets'));
}
```

- [ ] **Step 2: Run the tests — expect FAIL**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/RbacTest.php --filter=test_five_roles_exist_after_seeding
```

Expected: FAIL — `Class "Database\Seeders\RolePermissionSeeder" not found`

- [ ] **Step 3: Create `database/seeders/RolePermissionSeeder.php`**

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Wipe permission cache before seeding
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_admin_panel',
            'manage_users',
            'manage_roles',
            'manage_leads',
            'manage_blog',
            'manage_tickets',
            'assign_tickets',
            'view_reports',
            'manage_accounting',
            'manage_employees',
            'manage_licenses',
            'assign_testers',
            'view_tester_dashboard',
            'manage_bug_reports',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $roleMap = [
            'super_admin' => $permissions,
            'admin'       => array_diff($permissions, ['manage_roles']),
            'support'     => ['view_admin_panel', 'manage_tickets', 'assign_tickets', 'manage_bug_reports'],
            'tester'      => ['view_tester_dashboard'],
            'customer'    => [],
        ];

        foreach ($roleMap as $roleName => $rolePerms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePerms);
        }

        // Upgrade the seeded admin user to super_admin
        $admin = User::where('email', 'admin@opeshealthsystems.com')->first();
        if ($admin) {
            $admin->syncRoles(['super_admin']);
            if (!$admin->employee_id) {
                $admin->update([
                    'employee_id' => 'EMP-2026-0001',
                    'department'  => 'Administration',
                    'position'    => 'System Administrator',
                    'hire_date'   => '2026-01-01',
                ]);
            }
        }
    }
}
```

- [ ] **Step 4: Update `database/seeders/DatabaseSeeder.php`**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}
```

- [ ] **Step 5: Run the role tests — expect PASS**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/RbacTest.php
```

Expected: All role tests PASS.

- [ ] **Step 6: Run the seeder on the real database**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan db:seed
```

Expected: Runs both seeders, no errors. Existing admin user now has `super_admin` role.

- [ ] **Step 7: Commit**

```bash
git add database/seeders/RolePermissionSeeder.php database/seeders/DatabaseSeeder.php tests/Feature/RbacTest.php
git commit -m "feat: seed 5 roles and 14 permissions, upgrade admin to super_admin"
```

---

## Task 4: Create RequireRole Middleware

**Files:**
- Create: `app/Http/Middleware/RequireRole.php`
- Modify: `bootstrap/app.php`

- [ ] **Step 1: Write failing middleware tests**

Add to `tests/Feature/RbacTest.php`:

```php
public function test_unauthenticated_user_is_redirected_from_role_guarded_route(): void
{
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    // Add a test route (we test the middleware directly via a fake route)
    \Illuminate\Support\Facades\Route::get('/test-admin-only', fn () => 'ok')
        ->middleware(['web', 'role:admin']);

    $response = $this->get('/test-admin-only');
    $response->assertRedirect('/login');
}

public function test_user_with_wrong_role_gets_403(): void
{
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    \Illuminate\Support\Facades\Route::get('/test-admin-only', fn () => 'ok')
        ->middleware(['web', 'role:admin']);

    $customer = User::factory()->create();
    $customer->assignRole('customer');

    $this->actingAs($customer)
        ->get('/test-admin-only')
        ->assertForbidden();
}

public function test_user_with_correct_role_passes_middleware(): void
{
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    \Illuminate\Support\Facades\Route::get('/test-admin-only', fn () => 'ok')
        ->middleware(['web', 'role:admin']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get('/test-admin-only')
        ->assertOk()
        ->assertSee('ok');
}
```

Also add at the top of `RbacTest.php` after `use Tests\TestCase;`:
```php
use App\Models\User;
```

- [ ] **Step 2: Run the tests — expect FAIL**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/RbacTest.php --filter=test_unauthenticated_user_is_redirected_from_role_guarded_route
```

Expected: FAIL — `Target class [role] does not exist`

- [ ] **Step 3: Create `app/Http/Middleware/RequireRole.php`**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->hasAnyRole($roles)) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
```

- [ ] **Step 4: Register the middleware alias in `bootstrap/app.php`**

Replace the `->withMiddleware(...)` block:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'setlocale' => \App\Http\Middleware\SetLocale::class,
        'role'      => \App\Http\Middleware\RequireRole::class,
    ]);
})
```

- [ ] **Step 5: Run the middleware tests — expect PASS**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/RbacTest.php
```

Expected: All tests PASS.

- [ ] **Step 6: Run the full test suite**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test
```

Expected: All previously passing tests still pass.

- [ ] **Step 7: Commit**

```bash
git add app/Http/Middleware/RequireRole.php bootstrap/app.php tests/Feature/RbacTest.php
git commit -m "feat: add RequireRole middleware and register role alias"
```

---

## Task 5: Test canAccessPanel Restriction

**Files:**
- Modify: `tests/Feature/RbacTest.php`

The `canAccessPanel` change was made in Task 2. This task adds a test to lock it down.

- [ ] **Step 1: Add the canAccessPanel test**

Add to `tests/Feature/RbacTest.php`:

```php
public function test_customer_cannot_access_filament_panel(): void
{
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    $customer = User::factory()->create();
    $customer->assignRole('customer');

    $this->assertFalse($customer->canAccessPanel(
        app(\Filament\Panel::class)
    ));
}

public function test_admin_can_access_filament_panel(): void
{
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->assertTrue($admin->canAccessPanel(
        app(\Filament\Panel::class)
    ));
}

public function test_super_admin_can_access_filament_panel(): void
{
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super_admin');

    $this->assertTrue($superAdmin->canAccessPanel(
        app(\Filament\Panel::class)
    ));
}
```

- [ ] **Step 2: Run the tests — expect PASS**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/RbacTest.php --filter=test_customer_cannot_access_filament_panel
```

Expected: PASS

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/RbacTest.php
git commit -m "test: assert canAccessPanel RBAC restrictions"
```

---

## Task 6: Create Filament UserResource

**Files:**
- Create: `app/Filament/Resources/UserResource.php`
- Create: `app/Filament/Resources/UserResource/Pages/ListUsers.php`
- Create: `app/Filament/Resources/UserResource/Pages/CreateUser.php`
- Create: `app/Filament/Resources/UserResource/Pages/EditUser.php`

- [ ] **Step 1: Create `app/Filament/Resources/UserResource.php`**

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Personal Information')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignorable: fn (?User $record) => $record)
                        ->maxLength(150),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(30),
                    Forms\Components\FileUpload::make('avatar')
                        ->image()
                        ->directory('avatars')
                        ->nullable(),
                ]),

            Forms\Components\Section::make('Employment')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('employee_id')
                        ->label('Employee ID')
                        ->default(fn () => 'EMP-'.date('Y').'-'.str_pad(
                            User::whereNotNull('employee_id')->count() + 1,
                            4, '0', STR_PAD_LEFT
                        ))
                        ->unique(ignorable: fn (?User $record) => $record)
                        ->nullable()
                        ->helperText('Auto-filled. Leave blank for customer accounts.'),
                    Forms\Components\DatePicker::make('hire_date')
                        ->nullable(),
                    Forms\Components\TextInput::make('department')
                        ->maxLength(80)
                        ->nullable(),
                    Forms\Components\TextInput::make('position')
                        ->maxLength(80)
                        ->nullable(),
                ]),

            Forms\Components\Section::make('Account & Roles')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context) => $context === 'create')
                        ->minLength(8)
                        ->maxLength(64),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                    Forms\Components\CheckboxList::make('roles')
                        ->relationship('roles', 'name')
                        ->columns(2)
                        ->columnSpanFull()
                        ->searchable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->defaultImageUrl(fn (User $u) => 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=00C896&color=fff')
                    ->size(36),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('EMP ID')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('department')
                    ->placeholder('—'),
                Tables\Columns\BadgeColumn::make('roles.name')
                    ->label('Roles')
                    ->separator(','),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 2: Create `app/Filament/Resources/UserResource/Pages/ListUsers.php`**

```php
<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
```

- [ ] **Step 3: Create `app/Filament/Resources/UserResource/Pages/CreateUser.php`**

```php
<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

- [ ] **Step 4: Create `app/Filament/Resources/UserResource/Pages/EditUser.php`**

```php
<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

- [ ] **Step 5: Clear Filament cache and verify the resource appears**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan filament:optimize-clear
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan config:clear
```

Then visit `http://ohs.test/admin` in a browser. Log in as `admin@opeshealthsystems.com` / `OPESadmin2026!`. Verify "Users" appears under "People" in the sidebar.

- [ ] **Step 6: Run the full test suite**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test
```

Expected: All tests PASS.

- [ ] **Step 7: Commit**

```bash
git add app/Filament/Resources/UserResource.php app/Filament/Resources/UserResource/
git commit -m "feat: add Filament UserResource with employee fields and role assignment"
```

---

## Task 7: Create Filament RoleResource (read-only overview)

**Files:**
- Create: `app/Filament/Resources/RoleResource.php`
- Create: `app/Filament/Resources/RoleResource/Pages/ListRoles.php`
- Create: `app/Filament/Resources/RoleResource/Pages/ViewRole.php`

- [ ] **Step 1: Create `app/Filament/Resources/RoleResource.php`**

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Roles & Permissions';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Role Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->disabled(),
                    Forms\Components\CheckboxList::make('permissions')
                        ->relationship('permissions', 'name')
                        ->columns(3)
                        ->disabled(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'super_admin' => 'danger',
                        'admin'       => 'warning',
                        'support'     => 'info',
                        'tester'      => 'success',
                        'customer'    => 'gray',
                        default       => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->badge()
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRoles::route('/'),
            'view'   => Pages\ViewRole::route('/{record}'),
        ];
    }
}
```

- [ ] **Step 2: Create `app/Filament/Resources/RoleResource/Pages/ListRoles.php`**

```php
<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;
}
```

- [ ] **Step 3: Create `app/Filament/Resources/RoleResource/Pages/ViewRole.php`**

```php
<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;
}
```

- [ ] **Step 4: Clear caches and verify in browser**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan filament:optimize-clear
```

Visit `http://ohs.test/admin`. Log in as super_admin. Confirm "Roles & Permissions" appears in the "People" group with 5 roles listed. Click on `super_admin` — all 14 permissions should be shown.

- [ ] **Step 5: Run full test suite**

```bash
C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test
```

Expected: All tests PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Filament/Resources/RoleResource.php app/Filament/Resources/RoleResource/
git commit -m "feat: add read-only Filament RoleResource for super_admin"
```

---

## Self-Review

### 1. Spec coverage

| Requirement | Covered |
|---|---|
| RBAC with 5 roles | ✅ Task 3 — super_admin, admin, support, tester, customer |
| Employee ID generation | ✅ Task 6 — auto-suggested in UserResource form |
| Employee fields (department, position, hire_date) | ✅ Task 2 migration + Task 6 form |
| Middleware to guard routes | ✅ Task 4 — `role:admin` etc. |
| Admin panel restricted to staff | ✅ Task 2 — `canAccessPanel` updated |
| Filament user management UI | ✅ Task 6 — UserResource with role assignment |
| Role overview in admin | ✅ Task 7 — RoleResource (super_admin only) |
| Admin user upgraded to super_admin | ✅ Task 3 — RolePermissionSeeder |
| Tests for all behavior | ✅ RbacTest.php covers tables, roles, permissions, middleware, canAccessPanel |

### 2. Placeholder scan

- No "TBD" or "TODO" in any step.
- All code blocks are complete.
- All file paths are exact.

### 3. Type consistency

- `User` model uses `hasAnyRole()` consistently in `canAccessPanel` and in Resource `canAccess()`.
- `RolePermissionSeeder` uses `syncPermissions()` and `syncRoles()` — both are Spatie API methods.
- `RequireRole` middleware uses `hasAnyRole($roles)` matching Spatie's variadic API.

---

## What Comes Next

This plan produces a working RBAC foundation. The subsequent plans build on top of it:

| Next Plan | What it adds |
|---|---|
| **B** — Customer Portal | Customer registration, login, dashboard, profile (uses `customer` role) |
| **C** — License Management | `licenses` table, purchase workflow, expiry tracking (uses `manage_licenses` permission) |
| **D** — Ticket & Bug System | `tickets` table, bug reports, ticket replies (uses `manage_tickets` permission) |
| **E** — Tester Assignment | Test cycles, task assignment to doctors/nurses (uses `assign_testers` permission) |
| **F** — Accounting Module | Sales, invoices, employee payroll, revenue dashboard (uses `manage_accounting` permission) |
