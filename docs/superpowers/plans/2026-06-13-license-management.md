# License Management — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a software license management system — admins issue licenses for OPES products to customers, customers view their active licenses in the portal.

**Architecture:** Single `licenses` table. Products are config-driven (no DB table), so `product_slug` is stored as a plain string referencing `config('products')`. License lifecycle: `active` → `expiring` (within 30 days of end_date) → `expired`, or manually `suspended` / `cancelled`. License keys are auto-generated UUID strings. Admins manage licenses via a Filament resource; customers see their licenses in the portal (replaces the "Coming soon" Licenses nav link). No payment/checkout workflow — admin manually creates licenses.

**Tech Stack:** Laravel 13, PHP 8.3, Filament v3.3, Spatie Laravel Permission, Blade/Tailwind CSS v4, PHPUnit / SQLite in-memory

---

## File Map

### New files
- `database/migrations/2026_06_13_230000_create_licenses_table.php`
- `app/Models/License.php`
- `app/Filament/Resources/LicenseResource.php`
- `app/Filament/Resources/LicenseResource/Pages/ListLicenses.php`
- `app/Filament/Resources/LicenseResource/Pages/CreateLicense.php`
- `app/Filament/Resources/LicenseResource/Pages/EditLicense.php`
- `app/Http/Controllers/Customer/LicenseController.php`
- `resources/views/customer/licenses/index.blade.php`
- `resources/views/customer/licenses/show.blade.php`
- `tests/Feature/LicenseManagementTest.php`

### Modified files
- `database/seeders/RolePermissionSeeder.php` — add `manage_licenses` permission (already in permissions array — verify it's there)
- `routes/web.php` — add `/{locale}/customer/licenses` routes
- `resources/views/components/layouts/customer.blade.php` — replace Licenses "Coming soon" span with real link

---

## Task 1: Migration + Model + Tests

**Files:**
- Create: `database/migrations/2026_06_13_230000_create_licenses_table.php`
- Create: `app/Models/License.php`
- Create: `tests/Feature/LicenseManagementTest.php`

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/LicenseManagementTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class LicenseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_licenses_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('licenses'));
    }

    public function test_license_can_be_created(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $admin    = User::factory()->create();
        $admin->assignRole('admin');

        $license = License::create([
            'user_id'      => $customer->id,
            'issued_by'    => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'license_key'  => 'OPES-' . strtoupper(substr(md5('test'), 0, 16)),
            'plan'         => 'professional',
            'seats'        => 5,
            'status'       => 'active',
            'start_date'   => now()->toDateString(),
            'end_date'     => now()->addYear()->toDateString(),
            'price'        => 150000,
            'currency'     => 'XAF',
        ]);

        $this->assertDatabaseHas('licenses', [
            'product_slug' => 'opescare',
            'status'       => 'active',
            'user_id'      => $customer->id,
        ]);

        $this->assertEquals($customer->id, $license->customer->id);
        $this->assertEquals($admin->id, $license->issuer->id);
    }

    public function test_license_key_is_unique(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $admin    = User::factory()->create();
        $admin->assignRole('admin');

        $key = 'OPES-UNIQUE-KEY-12345';

        License::create([
            'user_id'      => $customer->id,
            'issued_by'    => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'license_key'  => $key,
            'plan'         => 'standard',
            'seats'        => 1,
            'status'       => 'active',
            'start_date'   => now()->toDateString(),
            'end_date'     => now()->addYear()->toDateString(),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        License::create([
            'user_id'      => $customer->id,
            'issued_by'    => $admin->id,
            'product_slug' => 'opes-emr',
            'product_name' => 'OPES EMR',
            'license_key'  => $key,
            'plan'         => 'standard',
            'seats'        => 1,
            'status'       => 'active',
            'start_date'   => now()->toDateString(),
            'end_date'     => now()->addYear()->toDateString(),
        ]);
    }

    public function test_license_generate_key_produces_unique_strings(): void
    {
        $key1 = License::generateKey();
        $key2 = License::generateKey();

        $this->assertNotEquals($key1, $key2);
        $this->assertStringStartsWith('OPES-', $key1);
    }

    public function test_license_is_expiring_soon(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $admin    = User::factory()->create();
        $admin->assignRole('admin');

        $license = License::create([
            'user_id'      => $customer->id,
            'issued_by'    => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'license_key'  => License::generateKey(),
            'plan'         => 'standard',
            'seats'        => 1,
            'status'       => 'active',
            'start_date'   => now()->subMonth()->toDateString(),
            'end_date'     => now()->addDays(15)->toDateString(),
        ]);

        $this->assertTrue($license->isExpiringSoon());
    }

    public function test_manage_licenses_permission_exists(): void
    {
        $this->assertDatabaseHas('permissions', ['name' => 'manage_licenses']);
    }

    public function test_admin_has_manage_licenses_permission(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->assertTrue($admin->hasPermissionTo('manage_licenses'));
    }
}
```

- [ ] **Step 2: Run the tests — expect FAIL**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/LicenseManagementTest.php
```

Expected: FAIL — table doesn't exist.

- [ ] **Step 3: Create `database/migrations/2026_06_13_230000_create_licenses_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
            $table->string('product_slug');
            $table->string('product_name');
            $table->string('license_key')->unique();
            $table->string('plan')->default('standard');
            $table->unsignedSmallInteger('seats')->default(1);
            $table->enum('status', ['active', 'suspended', 'expired', 'cancelled'])->default('active');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('price')->nullable();
            $table->string('currency', 10)->default('XAF');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
```

- [ ] **Step 4: Create `app/Models/License.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class License extends Model
{
    protected $fillable = [
        'user_id', 'issued_by', 'product_slug', 'product_name',
        'license_key', 'plan', 'seats', 'status',
        'start_date', 'end_date', 'price', 'currency', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'seats'      => 'integer',
        'price'      => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public static function generateKey(): string
    {
        return 'OPES-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->status === 'active'
            && $this->end_date !== null
            && $this->end_date->isFuture()
            && $this->end_date->diffInDays(now()) <= $days;
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->end_date !== null && $this->end_date->isPast());
    }

    public static function planLabel(string $plan): string
    {
        return match ($plan) {
            'starter'      => 'Starter',
            'standard'     => 'Standard',
            'professional' => 'Professional',
            'enterprise'   => 'Enterprise',
            default        => ucfirst($plan),
        };
    }

    public static function planOptions(): array
    {
        return [
            'starter'      => 'Starter',
            'standard'     => 'Standard',
            'professional' => 'Professional',
            'enterprise'   => 'Enterprise',
        ];
    }
}
```

- [ ] **Step 5: Verify `manage_licenses` is in RolePermissionSeeder**

Read `database/seeders/RolePermissionSeeder.php`. Confirm `'manage_licenses'` is already in the `$permissions` array. If it is, no change needed. If it's missing, add it.

- [ ] **Step 6: Run migrations**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan migrate
```

- [ ] **Step 7: Run the tests — expect PASS**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/LicenseManagementTest.php
```

Expected: 7 tests pass.

- [ ] **Step 8: Run full suite — no regressions**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 61 tests pass (54 + 7).

- [ ] **Step 9: Commit**

```
git add database/migrations/2026_06_13_230000_create_licenses_table.php app/Models/License.php tests/Feature/LicenseManagementTest.php
git commit -m "feat: add licenses table, License model, and license management tests"
```

---

## Task 2: Filament LicenseResource (Admin)

**Files:**
- Create: `app/Filament/Resources/LicenseResource.php`
- Create: `app/Filament/Resources/LicenseResource/Pages/ListLicenses.php`
- Create: `app/Filament/Resources/LicenseResource/Pages/CreateLicense.php`
- Create: `app/Filament/Resources/LicenseResource/Pages/EditLicense.php`

The Filament resource lets admins create and manage licenses. When creating, the license key is auto-generated but shown as an editable field (so admin can override). The product is selected from a dropdown built from `config('products')` combined with `config('products_specialist')`.

- [ ] **Step 1: Read the products config**

Read `config/products.php` to understand the array structure (should have slug and name keys per product).

- [ ] **Step 2: Create `app/Filament/Resources/LicenseResource.php`**

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LicenseResource\Pages;
use App\Models\License;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Licenses';
    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'support']) ?? false;
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
        return $form->schema([
            Forms\Components\Section::make('License Details')->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Customer')
                    ->options(
                        User::role('customer')->orderBy('name')->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('product_slug')
                    ->label('Product')
                    ->options(static::getProductOptions())
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                        if (!$state) return;
                        $options = static::getProductOptions();
                        if (isset($options[$state])) {
                            $set('product_name', $options[$state]);
                        }
                    }),

                Forms\Components\Hidden::make('product_name'),

                Forms\Components\TextInput::make('license_key')
                    ->label('License Key')
                    ->required()
                    ->default(fn () => License::generateKey())
                    ->maxLength(50)
                    ->columnSpanFull(),

                Forms\Components\Select::make('plan')
                    ->options(License::planOptions())
                    ->default('standard')
                    ->required(),

                Forms\Components\TextInput::make('seats')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(9999)
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'active'    => 'Active',
                        'suspended' => 'Suspended',
                        'expired'   => 'Expired',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('active')
                    ->required(),
            ])->columns(2),

            Forms\Components\Section::make('Validity Period')->schema([
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->default(now()->toDateString()),

                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->default(now()->addYear()->toDateString())
                    ->after('start_date'),
            ])->columns(2),

            Forms\Components\Section::make('Pricing (optional)')->schema([
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->nullable()
                    ->prefix('XAF')
                    ->helperText('Leave blank for complementary licenses'),

                Forms\Components\Select::make('currency')
                    ->options(['XAF' => 'XAF (CFA Franc)', 'USD' => 'USD', 'EUR' => 'EUR'])
                    ->default('XAF'),

                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(2)->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('license_key')
                    ->label('Key')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('plan')
                    ->badge()
                    ->formatStateUsing(fn ($state) => License::planLabel($state))
                    ->color(fn ($state) => match ($state) {
                        'starter'      => 'gray',
                        'standard'     => 'info',
                        'professional' => 'warning',
                        'enterprise'   => 'success',
                        default        => 'gray',
                    }),

                Tables\Columns\TextColumn::make('seats')
                    ->label('Seats')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'success',
                        'suspended' => 'warning',
                        'expired'   => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Expires')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isExpiringSoon() ? 'warning' : null),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active'    => 'Active',
                        'suspended' => 'Suspended',
                        'expired'   => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('plan')
                    ->options(License::planOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->hidden(fn (License $record) => $record->status !== 'active')
                    ->action(fn (License $record) => $record->update(['status' => 'suspended'])),
                Tables\Actions\Action::make('reactivate')
                    ->label('Reactivate')
                    ->icon('heroicon-o-play-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (License $record) => $record->status !== 'suspended')
                    ->action(fn (License $record) => $record->update(['status' => 'active'])),
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
            'index'  => Pages\ListLicenses::route('/'),
            'create' => Pages\CreateLicense::route('/create'),
            'edit'   => Pages\EditLicense::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 3: Create the 3 page classes**

Create `app/Filament/Resources/LicenseResource/Pages/ListLicenses.php`:

```php
<?php

namespace App\Filament\Resources\LicenseResource\Pages;

use App\Filament\Resources\LicenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLicenses extends ListRecords
{
    protected static string $resource = LicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
```

Create `app/Filament/Resources/LicenseResource/Pages/CreateLicense.php`:

```php
<?php

namespace App\Filament\Resources\LicenseResource\Pages;

use App\Filament\Resources\LicenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLicense extends CreateRecord
{
    protected static string $resource = LicenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['issued_by'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

Create `app/Filament/Resources/LicenseResource/Pages/EditLicense.php`:

```php
<?php

namespace App\Filament\Resources\LicenseResource\Pages;

use App\Filament\Resources\LicenseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLicense extends EditRecord
{
    protected static string $resource = LicenseResource::class;

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

- [ ] **Step 4: Run full test suite — no regressions**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 61 tests pass.

- [ ] **Step 5: Commit**

```
git add app/Filament/Resources/LicenseResource.php app/Filament/Resources/LicenseResource/
git commit -m "feat: add Filament LicenseResource for admin license management"
```

---

## Task 3: Customer Portal — My Licenses

**Files:**
- Create: `app/Http/Controllers/Customer/LicenseController.php`
- Create: `resources/views/customer/licenses/index.blade.php`
- Create: `resources/views/customer/licenses/show.blade.php`
- Modify: `routes/web.php` — add customer license routes
- Modify: `resources/views/components/layouts/customer.blade.php` — replace Licenses "Coming soon" span with real link

- [ ] **Step 1: Add customer license tests**

Add to `tests/Feature/LicenseManagementTest.php` inside the class before the closing `}`:

```php
    public function test_customer_can_view_their_licenses(): void
    {
        $admin    = User::factory()->create();
        $admin->assignRole('admin');
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        License::create([
            'user_id'      => $customer->id,
            'issued_by'    => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'license_key'  => License::generateKey(),
            'plan'         => 'professional',
            'seats'        => 3,
            'status'       => 'active',
            'start_date'   => now()->toDateString(),
            'end_date'     => now()->addYear()->toDateString(),
        ]);

        $this->actingAs($customer)
            ->get('/en/customer/licenses')
            ->assertOk()
            ->assertSee('OPESCare');
    }

    public function test_customer_cannot_see_another_customers_license(): void
    {
        $admin     = User::factory()->create();
        $admin->assignRole('admin');
        $customer1 = User::factory()->create();
        $customer1->assignRole('customer');
        $customer2 = User::factory()->create();
        $customer2->assignRole('customer');

        $license = License::create([
            'user_id'      => $customer1->id,
            'issued_by'    => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'license_key'  => License::generateKey(),
            'plan'         => 'standard',
            'seats'        => 1,
            'status'       => 'active',
            'start_date'   => now()->toDateString(),
            'end_date'     => now()->addYear()->toDateString(),
        ]);

        $this->actingAs($customer2)
            ->get('/en/customer/licenses/' . $license->id)
            ->assertForbidden();
    }
```

- [ ] **Step 2: Create `app/Http/Controllers/Customer/LicenseController.php`**

```php
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Support\Facades\Auth;

class LicenseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $licenses = License::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('customer.licenses.index', compact('licenses'));
    }

    public function show(int $id)
    {
        $user    = Auth::user();
        $license = License::findOrFail($id);

        abort_if((int) $license->user_id !== $user->id, 403);

        return view('customer.licenses.show', compact('license'));
    }
}
```

- [ ] **Step 3: Add customer license routes to `routes/web.php`**

Inside the `Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')` group, add after the documents routes:

```php
Route::get('/licenses',      [\App\Http\Controllers\Customer\LicenseController::class, 'index'])->name('licenses');
Route::get('/licenses/{id}', [\App\Http\Controllers\Customer\LicenseController::class, 'show'])->name('licenses.show');
```

- [ ] **Step 4: Create `resources/views/customer/licenses/index.blade.php`**

```html
<x-layouts.customer title="My Licenses">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">My Licenses</h1>
            <p class="cp-page-subtitle">Software licenses issued to your account</p>
        </div>
    </div>

    @if($licenses->isEmpty())
        <div class="cp-section-card" style="text-align:center;padding:3rem;">
            <div class="cp-empty-state">
                <i data-lucide="key" style="width:48px;height:48px;color:#334155"></i>
                <p>No licenses yet.</p>
                <p style="font-size:0.8125rem">Software licenses issued to your account will appear here.</p>
            </div>
        </div>
    @else
        <div class="cp-section-card" style="padding:0;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #334155;">
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Product</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Plan</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Seats</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Expires</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($licenses as $license)
                    @php
                        $statusColor = match($license->status) {
                            'active'    => '#00C896',
                            'suspended' => '#eab308',
                            'expired'   => '#ef4444',
                            'cancelled' => '#64748b',
                            default     => '#94a3b8',
                        };
                        $expiring = $license->isExpiringSoon();
                    @endphp
                    <tr style="border-bottom:1px solid #1e293b;">
                        <td style="padding:0.75rem;">
                            <div style="color:#e2e8f0;font-size:0.875rem;font-weight:500;">{{ $license->product_name }}</div>
                            <div style="color:#475569;font-size:0.75rem;font-family:monospace;">{{ $license->license_key }}</div>
                        </td>
                        <td style="padding:0.75rem;">
                            <span style="background:rgba(100,116,139,0.15);color:#94a3b8;font-size:0.7rem;font-weight:600;padding:0.2rem 0.5rem;border-radius:20px;text-transform:uppercase;letter-spacing:0.04em;">
                                {{ \App\Models\License::planLabel($license->plan) }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;color:#94a3b8;font-size:0.875rem;text-align:center;">{{ $license->seats }}</td>
                        <td style="padding:0.75rem;">
                            <span style="color:{{ $statusColor }};font-size:0.8125rem;font-weight:600;text-transform:capitalize;">
                                {{ $license->status }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;color:{{ $expiring ? '#eab308' : '#64748b' }};font-size:0.8125rem;">
                            {{ $license->end_date->format('d M Y') }}
                            @if($expiring)
                                <span style="font-size:0.7rem;"> ⚠ Expiring soon</span>
                            @endif
                        </td>
                        <td style="padding:0.75rem;text-align:right;">
                            <a href="{{ route('customer.licenses.show', ['locale' => app()->getLocale(), 'id' => $license->id]) }}"
                               class="cp-btn-outline" style="font-size:0.75rem;padding:0.375rem 0.75rem;">Details</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:1rem 0.75rem 0;">
                {{ $licenses->links() }}
            </div>
        </div>
    @endif
</x-layouts.customer>
```

- [ ] **Step 5: Create `resources/views/customer/licenses/show.blade.php`**

```html
<x-layouts.customer title="{{ $license->product_name }} License">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">{{ $license->product_name }}</h1>
            <p class="cp-page-subtitle">{{ \App\Models\License::planLabel($license->plan) }} Plan &middot; {{ $license->seats }} seat(s)</p>
        </div>
        <a href="{{ route('customer.licenses', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">
            &larr; Back
        </a>
    </div>

    @php
        $statusColor = match($license->status) {
            'active'    => '#00C896',
            'suspended' => '#eab308',
            'expired'   => '#ef4444',
            'cancelled' => '#64748b',
            default     => '#94a3b8',
        };
    @endphp

    @if($license->isExpiringSoon())
        <div style="background:rgba(234,179,8,0.08);border:1px solid rgba(234,179,8,0.25);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;">
            <p style="color:#eab308;font-weight:600;font-size:0.9rem;margin:0;">&#9888; License expiring soon</p>
            <p style="color:#64748b;font-size:0.8rem;margin:0.25rem 0 0;">Your license expires on {{ $license->end_date->format('d M Y') }}. Contact support to renew.</p>
        </div>
    @endif

    <div class="cp-section-card">
        <h2 style="color:#e2e8f0;font-size:1rem;font-weight:600;margin-bottom:1.5rem;">License Details</h2>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">License Key</p>
                <p style="color:#00C896;font-family:monospace;font-size:0.9rem;word-break:break-all;">{{ $license->license_key }}</p>
            </div>
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">Status</p>
                <p style="color:{{ $statusColor }};font-size:0.9rem;font-weight:600;text-transform:capitalize;">{{ $license->status }}</p>
            </div>
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">Product</p>
                <p style="color:#e2e8f0;font-size:0.875rem;">{{ $license->product_name }}</p>
            </div>
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">Plan</p>
                <p style="color:#e2e8f0;font-size:0.875rem;">{{ \App\Models\License::planLabel($license->plan) }}</p>
            </div>
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">Authorized Seats</p>
                <p style="color:#e2e8f0;font-size:0.875rem;">{{ $license->seats }}</p>
            </div>
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">Valid From</p>
                <p style="color:#e2e8f0;font-size:0.875rem;">{{ $license->start_date->format('d M Y') }}</p>
            </div>
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">Expires</p>
                <p style="color:{{ $license->isExpiringSoon() ? '#eab308' : '#e2e8f0' }};font-size:0.875rem;">{{ $license->end_date->format('d M Y') }}</p>
            </div>
            @if($license->price)
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">License Fee</p>
                <p style="color:#e2e8f0;font-size:0.875rem;">{{ $license->currency }} {{ number_format($license->price) }}</p>
            </div>
            @endif
        </div>

        @if($license->notes)
        <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid #334155;">
            <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Notes</p>
            <p style="color:#94a3b8;font-size:0.875rem;line-height:1.6;">{{ $license->notes }}</p>
        </div>
        @endif
    </div>

    <div class="cp-section-card" style="margin-top:1rem;">
        <p style="color:#64748b;font-size:0.8125rem;line-height:1.7;">
            Need to renew, upgrade, or have questions about this license? Contact
            <a href="mailto:support@opeshealthsystems.com" style="color:#00C896;">support@opeshealthsystems.com</a>
            or call +237 600 000 000.
        </p>
    </div>
</x-layouts.customer>
```

- [ ] **Step 6: Replace "Coming soon" Licenses span with real nav link**

In `resources/views/components/layouts/customer.blade.php`, find:

```html
            <span class="cp-nav-link cp-nav-link-disabled" title="Coming soon">
                <i data-lucide="key" style="width:16px;height:16px"></i> Licenses
            </span>
```

Replace with:

```html
            <a href="{{ route('customer.licenses', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.licenses*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="key" style="width:16px;height:16px"></i> Licenses
            </a>
```

- [ ] **Step 7: Run all tests**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 63 tests pass (61 + 2 new).

- [ ] **Step 8: Commit**

```
git add app/Http/Controllers/Customer/LicenseController.php resources/views/customer/licenses/ resources/views/components/layouts/customer.blade.php routes/web.php tests/Feature/LicenseManagementTest.php
git commit -m "feat: add customer portal My Licenses section replacing Coming Soon placeholder"
```

---

## Self-Review

### 1. Spec coverage

| Requirement | Covered |
|---|---|
| `licenses` table with all columns | ✅ Task 1 migration |
| `License` model with relationships + methods | ✅ Task 1 model |
| `manage_licenses` permission | ✅ Already in RolePermissionSeeder, Task 1 verifies |
| Auto-generated license keys `OPES-XXXX-XXXX-XXXX` | ✅ `License::generateKey()` |
| `isExpiringSoon()` method | ✅ Task 1 model |
| `isExpired()` method | ✅ Task 1 model |
| Filament admin resource with create/edit/suspend/reactivate | ✅ Task 2 |
| Product options from config | ✅ Task 2 `getProductOptions()` reads both products configs |
| `issued_by` set automatically on create | ✅ Task 2 `mutateFormDataBeforeCreate` |
| Customer portal license list | ✅ Task 3 |
| Customer portal license detail | ✅ Task 3 |
| Customer isolation (can't see other's licenses) | ✅ Task 3 controller |
| "Coming soon" nav link replaced | ✅ Task 3 |

### 2. Placeholder scan

None — all steps have actual code.

### 3. Type consistency

- `License::customer()` uses `user_id` FK — matches migration.
- `License::issuer()` uses `issued_by` FK — matches migration.
- `LicenseController::show()` casts `$license->user_id` with `(int)` before comparing to `$user->id` — type-safe.
- Route names: `customer.licenses`, `customer.licenses.show` — registered in web.php and used in views.
