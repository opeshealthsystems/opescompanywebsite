# Accounting Module — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build an invoice-based accounting module — admins create invoices with line items for customers; customers view their invoices and download PDFs; admins can mark invoices as sent, paid, or overdue from Filament.

**Architecture:** Two new tables (`invoices`, `invoice_items`). Invoice numbers are auto-generated (`INV-YYYY-NNNNN`) using the same DB transaction + lockForUpdate pattern as ticket reference numbers. Line items are managed via a Filament Repeater on the invoice form — no separate CRUD needed. PDF generation uses the already-installed `barryvdh/laravel-dompdf`. A single auth-protected PDF route serves both admins (via Filament action) and customers (via their portal). The customer portal gets an Invoices nav link and two views (list + detail). Tax rate is stored per-invoice; subtotal/tax/total are computed model accessors (not stored).

**Existing permissions (already seeded — DO NOT modify RolePermissionSeeder):**
- `manage_accounting` — super_admin, admin

**Tech Stack:** Laravel 13, PHP 8.3, Filament v3.3, Spatie Laravel Permission, barryvdh/laravel-dompdf, Blade/Tailwind CSS v4, SQLite in-memory tests

---

## File Map

### New files
- `database/migrations/2026_06_14_100000_create_invoices_table.php`
- `database/migrations/2026_06_14_101000_create_invoice_items_table.php`
- `app/Models/Invoice.php`
- `app/Models/InvoiceItem.php`
- `app/Filament/Resources/InvoiceResource.php`
- `app/Filament/Resources/InvoiceResource/Pages/ListInvoices.php`
- `app/Filament/Resources/InvoiceResource/Pages/CreateInvoice.php`
- `app/Filament/Resources/InvoiceResource/Pages/ViewInvoice.php`
- `app/Http/Controllers/InvoiceController.php` (PDF download, auth-gated)
- `app/Http/Controllers/Customer/InvoiceController.php`
- `resources/views/invoices/pdf.blade.php`
- `resources/views/customer/invoices/index.blade.php`
- `resources/views/customer/invoices/show.blade.php`
- `tests/Feature/AccountingModuleTest.php`

### Modified files
- `routes/web.php` — add invoice PDF route + customer invoice routes
- `resources/views/components/layouts/customer.blade.php` — add Invoices nav link

---

## Task 1: Migrations + Models + Tests

**Files:**
- Create: `database/migrations/2026_06_14_100000_create_invoices_table.php`
- Create: `database/migrations/2026_06_14_101000_create_invoice_items_table.php`
- Create: `app/Models/Invoice.php`
- Create: `app/Models/InvoiceItem.php`
- Create: `tests/Feature/AccountingModuleTest.php`

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/AccountingModuleTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AccountingModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_invoices_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('invoices'));
    }

    public function test_invoice_items_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('invoice_items'));
    }

    public function test_invoice_can_be_created_with_items(): void
    {
        $admin    = User::factory()->create();
        $admin->assignRole('admin');
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'issued_by'   => $admin->id,
            'status'      => 'draft',
            'currency'    => 'XAF',
            'tax_rate'    => 0,
            'due_date'    => now()->addDays(30)->toDateString(),
        ]);

        $invoice->items()->create([
            'description' => 'OPESCare Annual License',
            'quantity'    => 1,
            'unit_price'  => 500000,
            'total'       => 500000,
        ]);

        $this->assertDatabaseHas('invoices', [
            'customer_id' => $customer->id,
            'status'      => 'draft',
        ]);
        $this->assertDatabaseHas('invoice_items', [
            'description' => 'OPESCare Annual License',
            'unit_price'  => 500000,
        ]);
        $this->assertEquals(1, $invoice->items()->count());
    }

    public function test_invoice_number_is_auto_generated(): void
    {
        $admin    = User::factory()->create();
        $customer = User::factory()->create();

        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'issued_by'   => $admin->id,
            'status'      => 'draft',
            'currency'    => 'XAF',
            'tax_rate'    => 0,
            'due_date'    => now()->addDays(30)->toDateString(),
        ]);

        $this->assertMatchesRegularExpression(
            '/^INV-\d{4}-\d{5}$/',
            $invoice->invoice_number
        );
    }

    public function test_invoice_number_increments_sequentially(): void
    {
        $admin    = User::factory()->create();
        $customer = User::factory()->create();

        $first  = Invoice::create(['customer_id' => $customer->id, 'issued_by' => $admin->id, 'status' => 'draft', 'currency' => 'XAF', 'tax_rate' => 0, 'due_date' => now()->addDays(30)->toDateString()]);
        $second = Invoice::create(['customer_id' => $customer->id, 'issued_by' => $admin->id, 'status' => 'draft', 'currency' => 'XAF', 'tax_rate' => 0, 'due_date' => now()->addDays(30)->toDateString()]);

        $year = now()->year;
        $this->assertEquals("INV-{$year}-00001", $first->invoice_number);
        $this->assertEquals("INV-{$year}-00002", $second->invoice_number);
    }

    public function test_invoice_subtotal_is_computed(): void
    {
        $admin    = User::factory()->create();
        $customer = User::factory()->create();

        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'issued_by'   => $admin->id,
            'status'      => 'draft',
            'currency'    => 'XAF',
            'tax_rate'    => 10,
            'due_date'    => now()->addDays(30)->toDateString(),
        ]);

        $invoice->items()->createMany([
            ['description' => 'Item A', 'quantity' => 2, 'unit_price' => 100000, 'total' => 200000],
            ['description' => 'Item B', 'quantity' => 1, 'unit_price' => 50000,  'total' => 50000],
        ]);

        $invoice->load('items');

        $this->assertEquals(250000, $invoice->subtotal);
        $this->assertEquals(25000,  $invoice->taxAmount);
        $this->assertEquals(275000, $invoice->grandTotal);
    }

    public function test_manage_accounting_permission_exists(): void
    {
        $this->assertDatabaseHas('permissions', ['name' => 'manage_accounting']);
    }

    public function test_admin_has_manage_accounting_permission(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->assertTrue($admin->hasPermissionTo('manage_accounting'));
    }

    public function test_customer_can_view_their_invoices(): void
    {
        $admin    = User::factory()->create();
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        Invoice::create([
            'customer_id' => $customer->id,
            'issued_by'   => $admin->id,
            'status'      => 'sent',
            'currency'    => 'XAF',
            'tax_rate'    => 0,
            'due_date'    => now()->addDays(30)->toDateString(),
        ]);

        $this->actingAs($customer)
            ->get('/en/customer/invoices')
            ->assertOk();
    }

    public function test_customer_cannot_view_another_customers_invoice(): void
    {
        $admin     = User::factory()->create();
        $customer1 = User::factory()->create();
        $customer1->assignRole('customer');
        $customer2 = User::factory()->create();
        $customer2->assignRole('customer');

        $invoice = Invoice::create([
            'customer_id' => $customer1->id,
            'issued_by'   => $admin->id,
            'status'      => 'sent',
            'currency'    => 'XAF',
            'tax_rate'    => 0,
            'due_date'    => now()->addDays(30)->toDateString(),
        ]);

        $this->actingAs($customer2)
            ->get('/en/customer/invoices/' . $invoice->id)
            ->assertForbidden();
    }
}
```

- [ ] **Step 2: Run tests — expect FAIL (tables don't exist)**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/AccountingModuleTest.php
```

Expected: FAIL on table existence.

- [ ] **Step 3: Create `database/migrations/2026_06_14_100000_create_invoices_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('license_id')->nullable()->constrained('licenses')->nullOnDelete();
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->string('currency', 10)->default('XAF');
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
```

- [ ] **Step 4: Create `database/migrations/2026_06_14_101000_create_invoice_items_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->unsignedBigInteger('unit_price');
            $table->unsignedBigInteger('total');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
```

- [ ] **Step 5: Create `app/Models/Invoice.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id', 'issued_by', 'license_id',
        'status', 'currency', 'tax_rate', 'notes', 'due_date', 'paid_at',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'due_date' => 'date',
        'paid_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->year;
            $last = static::whereYear('created_at', $year)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('invoice_number');
            $seq = 1;
            if ($last && preg_match('/(\d+)$/', $last, $m)) {
                $seq = (int) $m[1] + 1;
            }
            return 'INV-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('id');
    }

    public function getSubtotalAttribute(): int
    {
        return $this->items->sum('total');
    }

    public function getTaxAmountAttribute(): int
    {
        return (int) round($this->subtotal * ($this->tax_rate / 100));
    }

    public function getGrandTotalAttribute(): int
    {
        return $this->subtotal + $this->taxAmount;
    }

    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isPast()
            && in_array($this->status, ['draft', 'sent']);
    }

    public static function statusOptions(): array
    {
        return [
            'draft'     => 'Draft',
            'sent'      => 'Sent',
            'paid'      => 'Paid',
            'overdue'   => 'Overdue',
            'cancelled' => 'Cancelled',
        ];
    }

    public function formatAmount(int $amount): string
    {
        return number_format($amount) . ' ' . $this->currency;
    }
}
```

- [ ] **Step 6: Create `app/Models/InvoiceItem.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'description', 'quantity', 'unit_price', 'total',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
```

- [ ] **Step 7: Run migrations**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan migrate
```

- [ ] **Step 8: Run model tests (non-HTTP) — expect PASS**

Run just the non-HTTP tests:
```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/AccountingModuleTest.php --filter="test_invoices_table_exists|test_invoice_items_table_exists|test_invoice_can_be_created_with_items|test_invoice_number_is_auto_generated|test_invoice_number_increments_sequentially|test_invoice_subtotal_is_computed|test_manage_accounting_permission_exists|test_admin_has_manage_accounting_permission"
```

Expected: 8 tests pass. The 2 HTTP tests (customer portal) will fail until Task 3.

- [ ] **Step 9: Run full suite**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 91 pass (83 existing + 8 new), 2 fail (HTTP customer tests pending Task 3).

- [ ] **Step 10: Commit**

```
git add database/migrations/2026_06_14_100000_create_invoices_table.php database/migrations/2026_06_14_101000_create_invoice_items_table.php app/Models/Invoice.php app/Models/InvoiceItem.php tests/Feature/AccountingModuleTest.php
git commit -m "feat: add invoices/invoice_items tables, Invoice and InvoiceItem models, and accounting tests"
```

---

## Task 2: Filament InvoiceResource + PDF View

**Files:**
- Create: `app/Filament/Resources/InvoiceResource.php`
- Create: `app/Filament/Resources/InvoiceResource/Pages/ListInvoices.php`
- Create: `app/Filament/Resources/InvoiceResource/Pages/CreateInvoice.php`
- Create: `app/Filament/Resources/InvoiceResource/Pages/ViewInvoice.php`
- Create: `app/Http/Controllers/InvoiceController.php`
- Create: `resources/views/invoices/pdf.blade.php`
- Modify: `routes/web.php` — add PDF download route

- [ ] **Step 1: Create `app/Filament/Resources/InvoiceResource.php`**

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\License;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 40;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo('manage_accounting') ?? false;
    }

    public static function canCreate(): bool { return static::canAccess(); }
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool { return static::canAccess(); }
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool { return static::canAccess(); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Invoice Details')->schema([
                Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->options(fn () => User::role('customer')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options(Invoice::statusOptions())
                    ->default('draft')
                    ->required(),

                Forms\Components\Select::make('license_id')
                    ->label('Linked License (optional)')
                    ->options(fn () => License::with('customer')
                        ->orderByDesc('created_at')
                        ->get()
                        ->mapWithKeys(fn ($l) => [$l->id => $l->license_key . ' — ' . $l->product_name])
                    )
                    ->searchable()
                    ->nullable(),

                Forms\Components\Select::make('currency')
                    ->options(['XAF' => 'XAF (CFA Franc)', 'USD' => 'USD', 'EUR' => 'EUR'])
                    ->default('XAF')
                    ->required(),

                Forms\Components\TextInput::make('tax_rate')
                    ->label('Tax Rate (%)')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100),

                Forms\Components\DatePicker::make('due_date')
                    ->nullable(),

                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->nullable()
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Line Items')->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required(),
                        Forms\Components\TextInput::make('unit_price')
                            ->label('Unit Price')
                            ->numeric()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $set('total', (int) $get('quantity') * (int) $get('unit_price'));
                            }),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(6)
                    ->defaultItems(1)
                    ->reorderable()
                    ->cloneable()
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'draft'     => 'gray',
                        'sent'      => 'info',
                        'paid'      => 'success',
                        'overdue'   => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('currency')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Invoice::statusOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view'   => Pages\ViewInvoice::route('/{record}'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
```

**Note:** `getPages()` references `Pages\EditInvoice` — you must also create an `EditInvoice` page (standard boilerplate, see below).

- [ ] **Step 2: Create `app/Filament/Resources/InvoiceResource/Pages/ListInvoices.php`**

```php
<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
```

- [ ] **Step 3: Create `app/Filament/Resources/InvoiceResource/Pages/CreateInvoice.php`**

```php
<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['issued_by'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
```

- [ ] **Step 4: Create `app/Filament/Resources/InvoiceResource/Pages/ViewInvoice.php`**

```php
<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('mark_sent')
                ->label('Mark as Sent')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->visible(fn () => $this->record->status === 'draft')
                ->action(function () {
                    $this->record->update(['status' => 'sent']);
                    Notification::make()->title('Invoice marked as sent.')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('mark_paid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => in_array($this->record->status, ['sent', 'overdue']))
                ->action(function () {
                    $this->record->update(['status' => 'paid', 'paid_at' => now()]);
                    Notification::make()->title('Invoice marked as paid.')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('mark_overdue')
                ->label('Mark as Overdue')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'sent')
                ->action(function () {
                    $this->record->update(['status' => 'overdue']);
                    Notification::make()->title('Invoice marked as overdue.')->warning()->send();
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn () => route('invoices.pdf', ['invoice' => $this->record->id]))
                ->openUrlInNewTab(),
        ];
    }
}
```

- [ ] **Step 5: Create `app/Filament/Resources/InvoiceResource/Pages/EditInvoice.php`**

```php
<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['issued_by']);
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
```

- [ ] **Step 6: Create `app/Http/Controllers/InvoiceController.php`** (PDF download, auth-gated for admin + customer ownership)

```php
<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function pdf(Request $request)
    {
        $user    = Auth::user();
        $id      = (int) $request->route('invoice');
        $invoice = Invoice::with('items', 'customer', 'issuer')->findOrFail($id);

        if ($user->hasAnyRole(['super_admin', 'admin', 'support'])) {
            // Admin/support can download any invoice
        } elseif ($user->hasRole('customer')) {
            abort_if((int) $invoice->customer_id !== $user->id, 403);
        } else {
            abort(403);
        }

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
```

- [ ] **Step 7: Create `resources/views/invoices/pdf.blade.php`**

Create directory `resources/views/invoices/`.

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; margin: 0; padding: 0; }
        .page { padding: 40px; }
        .header { display: table; width: 100%; margin-bottom: 30px; }
        .header-left { display: table-cell; width: 50%; }
        .header-right { display: table-cell; width: 50%; text-align: right; vertical-align: top; }
        .company-name { font-size: 20px; font-weight: bold; color: #0f172a; }
        .invoice-title { font-size: 24px; font-weight: bold; color: #00C896; margin-bottom: 4px; }
        .invoice-number { font-size: 14px; color: #64748b; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-draft     { background: #e2e8f0; color: #475569; }
        .status-sent      { background: #dbeafe; color: #1d4ed8; }
        .status-paid      { background: #d1fae5; color: #065f46; }
        .status-overdue   { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #e2e8f0; color: #475569; }
        .meta-table { width: 100%; margin-bottom: 30px; }
        .meta-table td { padding: 4px 8px; font-size: 11px; }
        .meta-label { color: #64748b; font-weight: bold; width: 130px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #0f172a; color: #e2e8f0; padding: 8px 10px; text-align: left; font-size: 11px; }
        .items-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .items-table tr:nth-child(even) td { background: #f8fafc; }
        .text-right { text-align: right; }
        .totals { margin-left: 60%; }
        .totals table { width: 100%; }
        .totals td { padding: 4px 8px; font-size: 12px; }
        .totals .label { color: #64748b; }
        .totals .amount { text-align: right; }
        .grand-total td { font-weight: bold; font-size: 14px; border-top: 2px solid #0f172a; padding-top: 8px; }
        .notes { margin-top: 30px; padding: 12px; background: #f8fafc; border-left: 3px solid #00C896; font-size: 11px; }
        .footer { margin-top: 40px; text-align: center; color: #94a3b8; font-size: 10px; border-top: 1px solid #e2e8f0; padding-top: 12px; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="header-left">
            <div class="company-name">OPES Health Systems</div>
            <div style="color:#64748b;font-size:11px;margin-top:4px;">Digital Health Solutions</div>
        </div>
        <div class="header-right">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            <div style="margin-top:6px;">
                <span class="status-badge status-{{ $invoice->status }}">{{ \App\Models\Invoice::statusOptions()[$invoice->status] ?? $invoice->status }}</span>
            </div>
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Bill To:</td>
            <td>{{ $invoice->customer?->name ?? 'N/A' }}<br>{{ $invoice->customer?->email ?? '' }}</td>
            <td class="meta-label">Invoice Date:</td>
            <td>{{ $invoice->created_at->format('d M Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Issued By:</td>
            <td>{{ $invoice->issuer?->name ?? 'OPES Health Systems' }}</td>
            <td class="meta-label">Due Date:</td>
            <td>{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
        </tr>
        @if($invoice->paid_at)
        <tr>
            <td class="meta-label">Paid On:</td>
            <td colspan="3">{{ $invoice->paid_at->format('d M Y') }}</td>
        </tr>
        @endif
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right" style="width:60px;">Qty</th>
                <th class="text-right" style="width:120px;">Unit Price</th>
                <th class="text-right" style="width:120px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price) }}</td>
                <td class="text-right">{{ number_format($item->total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td class="label">Subtotal</td>
                <td class="amount">{{ number_format($invoice->subtotal) }} {{ $invoice->currency }}</td>
            </tr>
            @if($invoice->tax_rate > 0)
            <tr>
                <td class="label">Tax ({{ $invoice->tax_rate }}%)</td>
                <td class="amount">{{ number_format($invoice->taxAmount) }} {{ $invoice->currency }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td>Total</td>
                <td class="amount">{{ number_format($invoice->grandTotal) }} {{ $invoice->currency }}</td>
            </tr>
        </table>
    </div>

    @if($invoice->notes)
    <div class="notes">
        <strong>Notes:</strong> {{ $invoice->notes }}
    </div>
    @endif

    <div class="footer">
        OPES Health Systems &mdash; {{ $invoice->invoice_number }} &mdash; Generated {{ now()->format('d M Y') }}
    </div>
</div>
</body>
</html>
```

- [ ] **Step 8: Add PDF route to `routes/web.php`**

Inside the existing `Route::middleware('auth')->group(...)` block (the one that already has `/documents/{document}/pdf`), add:

```php
    Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\InvoiceController::class, 'pdf'])->name('invoices.pdf');
```

- [ ] **Step 9: Run full test suite — should still be 91 pass, 2 fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

This task adds no new tests. Counts should not change from Task 1 end state.

- [ ] **Step 10: Commit**

```
git add app/Filament/Resources/InvoiceResource.php app/Filament/Resources/InvoiceResource/ app/Http/Controllers/InvoiceController.php resources/views/invoices/ routes/web.php
git commit -m "feat: add Filament InvoiceResource with line-item repeater, status actions, and PDF download"
```

---

## Task 3: Customer Portal Invoices Section

**Files:**
- Create: `app/Http/Controllers/Customer/InvoiceController.php`
- Create: `resources/views/customer/invoices/index.blade.php`
- Create: `resources/views/customer/invoices/show.blade.php`
- Modify: `routes/web.php` — add customer invoice routes
- Modify: `resources/views/components/layouts/customer.blade.php` — add Invoices nav link

- [ ] **Step 1: Create `app/Http/Controllers/Customer/InvoiceController.php`**

```php
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $invoices = Invoice::where('customer_id', $user->id)
            ->whereIn('status', ['sent', 'paid', 'overdue'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('customer.invoices.index', compact('invoices'));
    }

    public function show(Request $request)
    {
        $user    = Auth::user();
        $id      = (int) $request->route('id');
        $invoice = Invoice::with('items')->findOrFail($id);

        abort_if((int) $invoice->customer_id !== $user->id, 403);

        return view('customer.invoices.show', compact('invoice'));
    }
}
```

- [ ] **Step 2: Add customer invoice routes to `routes/web.php`**

Inside the customer portal route group (after the tickets routes), add:

```php
                Route::get('/invoices',      [\App\Http\Controllers\Customer\InvoiceController::class, 'index'])->name('invoices');
                Route::get('/invoices/{id}', [\App\Http\Controllers\Customer\InvoiceController::class, 'show'])->name('invoices.show');
```

- [ ] **Step 3: Add Invoices nav link to `resources/views/components/layouts/customer.blade.php`**

After the Licenses nav link (`<i data-lucide="key" ...>`) and before the Support link, add:

```html
            <a href="{{ route('customer.invoices', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.invoices*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="receipt" style="width:16px;height:16px"></i> Invoices
            </a>
```

- [ ] **Step 4: Create `resources/views/customer/invoices/index.blade.php`**

Create directory `resources/views/customer/invoices/`.

```html
<x-layouts.customer title="My Invoices">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">My Invoices</h1>
            <p class="cp-page-subtitle">Invoices issued to your account</p>
        </div>
    </div>

    @if($invoices->isEmpty())
        <div class="cp-section-card" style="text-align:center;padding:3rem;">
            <div class="cp-empty-state">
                <i data-lucide="receipt" style="width:48px;height:48px;color:#334155"></i>
                <p>No invoices yet.</p>
                <p style="font-size:0.8125rem">Invoices issued to your account will appear here.</p>
            </div>
        </div>
    @else
        <div class="cp-section-card" style="padding:0;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #334155;">
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Invoice #</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Due Date</th>
                        <th style="text-align:right;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Date</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    @php
                        $statusColor = match($invoice->status) {
                            'sent'      => '#3b82f6',
                            'paid'      => '#00C896',
                            'overdue'   => '#ef4444',
                            default     => '#94a3b8',
                        };
                    @endphp
                    <tr style="border-bottom:1px solid #1e293b;">
                        <td style="padding:0.75rem;color:#e2e8f0;font-size:0.875rem;font-weight:500;">{{ $invoice->invoice_number }}</td>
                        <td style="padding:0.75rem;">
                            <span style="color:{{ $statusColor }};font-size:0.8125rem;font-weight:600;text-transform:capitalize;">
                                {{ \App\Models\Invoice::statusOptions()[$invoice->status] ?? $invoice->status }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;color:#64748b;font-size:0.8125rem;">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                        <td style="padding:0.75rem;color:#64748b;font-size:0.8125rem;text-align:right;">{{ $invoice->created_at->format('d M Y') }}</td>
                        <td style="padding:0.75rem;text-align:right;">
                            <a href="{{ route('customer.invoices.show', ['locale' => app()->getLocale(), 'id' => $invoice->id]) }}"
                               class="cp-btn-outline" style="font-size:0.75rem;padding:0.375rem 0.75rem;">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:1rem 0.75rem 0;">
                {{ $invoices->links() }}
            </div>
        </div>
    @endif
</x-layouts.customer>
```

- [ ] **Step 5: Create `resources/views/customer/invoices/show.blade.php`**

```html
<x-layouts.customer title="{{ $invoice->invoice_number }}">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">{{ $invoice->invoice_number }}</h1>
            <p class="cp-page-subtitle">Invoice detail</p>
        </div>
        <div style="display:flex;gap:0.75rem;align-items:center;">
            <a href="{{ route('invoices.pdf', ['invoice' => $invoice->id]) }}"
               target="_blank" class="cp-btn-outline" style="font-size:0.875rem;">
                <i data-lucide="download" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Download PDF
            </a>
            <a href="{{ route('customer.invoices', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">&larr; Back</a>
        </div>
    </div>

    @php
        $statusColor = match($invoice->status) {
            'sent'      => '#3b82f6',
            'paid'      => '#00C896',
            'overdue'   => '#ef4444',
            default     => '#94a3b8',
        };
    @endphp

    @if($invoice->status === 'overdue')
        <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;">
            <p style="color:#ef4444;font-weight:600;font-size:0.875rem;margin:0;">&#9888; This invoice is overdue</p>
            <p style="color:#64748b;font-size:0.8rem;margin:0.25rem 0 0;">Due date was {{ $invoice->due_date?->format('d M Y') }}. Please contact support.</p>
        </div>
    @endif

    <div class="cp-section-grid" style="margin-bottom:1rem;">
        <div class="cp-section-card">
            <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Status</p>
            <p style="color:{{ $statusColor }};font-weight:700;font-size:1rem;">{{ \App\Models\Invoice::statusOptions()[$invoice->status] ?? $invoice->status }}</p>
        </div>
        <div class="cp-section-card">
            <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Due Date</p>
            <p style="color:#e2e8f0;font-weight:600;">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</p>
        </div>
        <div class="cp-section-card">
            <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Total</p>
            <p style="color:#00C896;font-weight:700;font-size:1.125rem;">{{ number_format($invoice->grandTotal) }} {{ $invoice->currency }}</p>
        </div>
        @if($invoice->paid_at)
        <div class="cp-section-card">
            <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Paid On</p>
            <p style="color:#00C896;font-weight:600;">{{ $invoice->paid_at->format('d M Y') }}</p>
        </div>
        @endif
    </div>

    <div class="cp-section-card">
        <h3 style="color:#e2e8f0;font-size:0.9rem;font-weight:600;margin-bottom:1rem;">Line Items</h3>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid #334155;">
                    <th style="text-align:left;padding:0.5rem 0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Description</th>
                    <th style="text-align:right;padding:0.5rem 0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Qty</th>
                    <th style="text-align:right;padding:0.5rem 0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Unit Price</th>
                    <th style="text-align:right;padding:0.5rem 0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr style="border-bottom:1px solid #1e293b;">
                    <td style="padding:0.625rem 0.75rem;color:#e2e8f0;font-size:0.875rem;">{{ $item->description }}</td>
                    <td style="padding:0.625rem 0.75rem;color:#94a3b8;font-size:0.875rem;text-align:right;">{{ $item->quantity }}</td>
                    <td style="padding:0.625rem 0.75rem;color:#94a3b8;font-size:0.875rem;text-align:right;">{{ number_format($item->unit_price) }}</td>
                    <td style="padding:0.625rem 0.75rem;color:#e2e8f0;font-size:0.875rem;text-align:right;font-weight:500;">{{ number_format($item->total) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:1px solid #334155;">
                    <td colspan="3" style="padding:0.625rem 0.75rem;color:#94a3b8;font-size:0.875rem;text-align:right;">Subtotal</td>
                    <td style="padding:0.625rem 0.75rem;color:#e2e8f0;font-size:0.875rem;text-align:right;">{{ number_format($invoice->subtotal) }}</td>
                </tr>
                @if($invoice->tax_rate > 0)
                <tr>
                    <td colspan="3" style="padding:0.375rem 0.75rem;color:#94a3b8;font-size:0.875rem;text-align:right;">Tax ({{ $invoice->tax_rate }}%)</td>
                    <td style="padding:0.375rem 0.75rem;color:#e2e8f0;font-size:0.875rem;text-align:right;">{{ number_format($invoice->taxAmount) }}</td>
                </tr>
                @endif
                <tr style="border-top:2px solid #334155;">
                    <td colspan="3" style="padding:0.75rem;color:#e2e8f0;font-size:0.9375rem;font-weight:700;text-align:right;">Total</td>
                    <td style="padding:0.75rem;color:#00C896;font-size:0.9375rem;font-weight:700;text-align:right;">{{ number_format($invoice->grandTotal) }} {{ $invoice->currency }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($invoice->notes)
    <div class="cp-section-card" style="margin-top:1rem;">
        <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Notes</p>
        <p style="color:#94a3b8;font-size:0.875rem;line-height:1.6;">{{ $invoice->notes }}</p>
    </div>
    @endif
</x-layouts.customer>
```

- [ ] **Step 6: Run all tests — expect 93 passing, 0 fail**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 93 tests pass (83 + 10 new), 0 fail.

- [ ] **Step 7: Commit**

```
git add app/Http/Controllers/Customer/InvoiceController.php resources/views/customer/invoices/ routes/web.php resources/views/components/layouts/customer.blade.php
git commit -m "feat: add customer portal Invoices section with list, detail, and PDF download"
```

---

## Self-Review

### 1. Spec coverage

| Requirement | Covered |
|---|---|
| `invoices` table with all columns | ✅ Task 1 migration |
| `invoice_items` table | ✅ Task 1 migration |
| Auto-generated `invoice_number` (INV-YYYY-NNNNN) | ✅ Task 1 model booted() |
| Race-safe number generation (lockForUpdate) | ✅ Task 1 DB::transaction |
| Subtotal / taxAmount / grandTotal as accessors | ✅ Task 1 model |
| Filament InvoiceResource gated by `manage_accounting` | ✅ Task 2 canAccess() |
| canCreate/Edit/Delete explicitly gated | ✅ Task 2 |
| Repeater for line items | ✅ Task 2 form |
| Mark Sent / Mark Paid / Mark Overdue header actions | ✅ Task 2 ViewInvoice |
| `issued_by` stamped on create, protected on edit | ✅ Task 2 Create + Edit mutate |
| PDF download (barryvdh/laravel-dompdf) | ✅ Task 2 InvoiceController + pdf.blade.php |
| Auth-protected PDF route (admin + customer ownership) | ✅ Task 2 |
| Customer portal Invoices list (sent/paid/overdue only) | ✅ Task 3 (drafts hidden from customers) |
| Customer portal Invoice detail with PDF link | ✅ Task 3 |
| Customer isolation (abort_if ownership) | ✅ Task 3 show() |
| Invoices nav link in customer layout | ✅ Task 3 customer.blade.php |
| 10 tests, 93 total | ✅ Task 1 + Task 3 |

### 2. Route ordering

Customer invoice routes: `/invoices` before `/invoices/{id}` — ✅ no literal/wildcard collision.

### 3. Authorization

- Admin PDF: `hasAnyRole(['super_admin', 'admin', 'support'])` — all staff can download
- Customer PDF: `abort_if` ownership check on `customer_id`
- Customer portal routes: `['auth', 'role:customer']` middleware
- Filament resource: `canAccess()` + explicit `canCreate/Edit/Delete`
