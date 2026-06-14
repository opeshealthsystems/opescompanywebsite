# Ticket & Support System — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a threaded support ticket system — customers submit tickets from their portal, support/admin manage and reply in Filament, everyone sees a full message thread.

**Architecture:** Two tables — `tickets` (the ticket itself) and `ticket_replies` (threaded messages). A single `type` enum covers all categories (support, billing, technical, bug_report, other), so one system serves customers, admins, and testers. Status lifecycle: `open` → `in_progress` → `pending_customer` → `resolved` → `closed`. Reference numbers: `TKT-2026-00001`. Customers can create tickets and reply; support/admin can see all tickets, reply, reassign, change status, and add internal-only notes. The disabled "Support" nav link in the customer layout becomes active.

**Existing permissions (already seeded — DO NOT modify RolePermissionSeeder):**
- `manage_tickets` — admin, support
- `assign_tickets` — admin, support
- `manage_bug_reports` — admin, support

**Tech Stack:** Laravel 13, PHP 8.3, Filament v3.3, Spatie Laravel Permission, Blade/Tailwind CSS v4, SQLite in-memory tests

---

## File Map

### New files
- `database/migrations/2026_06_13_240000_create_tickets_table.php`
- `database/migrations/2026_06_13_241000_create_ticket_replies_table.php`
- `app/Models/Ticket.php`
- `app/Models/TicketReply.php`
- `app/Filament/Resources/TicketResource.php`
- `app/Filament/Resources/TicketResource/Pages/ListTickets.php`
- `app/Filament/Resources/TicketResource/Pages/ViewTicket.php`
- `app/Filament/Resources/TicketResource/Pages/CreateTicket.php`
- `app/Http/Controllers/Customer/TicketController.php`
- `resources/views/customer/tickets/index.blade.php`
- `resources/views/customer/tickets/create.blade.php`
- `resources/views/customer/tickets/show.blade.php`
- `tests/Feature/TicketSystemTest.php`

### Modified files
- `routes/web.php` — add customer ticket routes
- `resources/views/components/layouts/customer.blade.php` — replace "Support" coming-soon span with real link

---

## Task 1: Migrations + Models + Tests

**Files:**
- Create: `database/migrations/2026_06_13_240000_create_tickets_table.php`
- Create: `database/migrations/2026_06_13_241000_create_ticket_replies_table.php`
- Create: `app/Models/Ticket.php`
- Create: `app/Models/TicketReply.php`
- Create: `tests/Feature/TicketSystemTest.php`

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/TicketSystemTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TicketSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_tickets_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('tickets'));
    }

    public function test_ticket_replies_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('ticket_replies'));
    }

    public function test_ticket_can_be_created_by_customer(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $ticket = Ticket::create([
            'user_id'     => $customer->id,
            'subject'     => 'Cannot access my dashboard',
            'description' => 'I get a 403 error when I log in.',
            'type'        => 'support',
            'priority'    => 'medium',
            'status'      => 'open',
        ]);

        $this->assertDatabaseHas('tickets', [
            'subject' => 'Cannot access my dashboard',
            'status'  => 'open',
            'user_id' => $customer->id,
        ]);

        $this->assertMatchesRegularExpression('/^TKT-\d{4}-\d{5}$/', $ticket->reference_number);
        $this->assertEquals($customer->id, $ticket->customer->id);
    }

    public function test_ticket_reply_can_be_added(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $support  = User::factory()->create();
        $support->assignRole('support');

        $ticket = Ticket::create([
            'user_id'     => $customer->id,
            'subject'     => 'Test ticket',
            'description' => 'Test description',
            'type'        => 'support',
            'priority'    => 'low',
            'status'      => 'open',
        ]);

        $reply = TicketReply::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $support->id,
            'body'        => 'We are looking into this.',
            'is_internal' => false,
        ]);

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'body'      => 'We are looking into this.',
        ]);

        $this->assertEquals($ticket->id, $reply->ticket->id);
        $this->assertEquals($support->id, $reply->author->id);
    }

    public function test_ticket_reference_number_increments(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $t1 = Ticket::create([
            'user_id'     => $customer->id,
            'subject'     => 'First ticket',
            'description' => 'First',
            'type'        => 'support',
            'priority'    => 'low',
            'status'      => 'open',
        ]);

        $t2 = Ticket::create([
            'user_id'     => $customer->id,
            'subject'     => 'Second ticket',
            'description' => 'Second',
            'type'        => 'support',
            'priority'    => 'low',
            'status'      => 'open',
        ]);

        $this->assertNotEquals($t1->reference_number, $t2->reference_number);
    }

    public function test_manage_tickets_permission_exists(): void
    {
        $this->assertDatabaseHas('permissions', ['name' => 'manage_tickets']);
    }

    public function test_support_has_manage_tickets_permission(): void
    {
        $support = User::factory()->create();
        $support->assignRole('support');
        $this->assertTrue($support->hasPermissionTo('manage_tickets'));
    }

    public function test_customer_can_view_their_tickets(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        Ticket::create([
            'user_id'     => $customer->id,
            'subject'     => 'My Billing Issue',
            'description' => 'I was charged twice.',
            'type'        => 'billing',
            'priority'    => 'high',
            'status'      => 'open',
        ]);

        $this->actingAs($customer)
            ->get('/en/customer/tickets')
            ->assertOk()
            ->assertSee('My Billing Issue');
    }

    public function test_customer_cannot_view_another_customers_ticket(): void
    {
        $customer1 = User::factory()->create();
        $customer1->assignRole('customer');
        $customer2 = User::factory()->create();
        $customer2->assignRole('customer');

        $ticket = Ticket::create([
            'user_id'     => $customer1->id,
            'subject'     => 'Private Ticket',
            'description' => 'Private content.',
            'type'        => 'support',
            'priority'    => 'low',
            'status'      => 'open',
        ]);

        $this->actingAs($customer2)
            ->get('/en/customer/tickets/' . $ticket->id)
            ->assertForbidden();
    }

    public function test_customer_can_create_ticket_via_form(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)
            ->post('/en/customer/tickets', [
                'subject'     => 'Test Support Request',
                'description' => 'I need help with something.',
                'type'        => 'support',
                'priority'    => 'medium',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'subject' => 'Test Support Request',
            'user_id' => $customer->id,
            'status'  => 'open',
        ]);
    }
}
```

- [ ] **Step 2: Run the tests — expect FAIL**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/TicketSystemTest.php
```

Expected: FAIL — tables don't exist.

- [ ] **Step 3: Create `database/migrations/2026_06_13_240000_create_tickets_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->enum('type', ['support', 'billing', 'technical', 'bug_report', 'other'])->default('support');
            $table->enum('status', ['open', 'in_progress', 'pending_customer', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
```

- [ ] **Step 4: Create `database/migrations/2026_06_13_241000_create_ticket_replies_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->boolean('is_internal')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_replies');
    }
};
```

- [ ] **Step 5: Create `app/Models/Ticket.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'reference_number', 'user_id', 'assigned_to',
        'subject', 'description', 'type', 'status', 'priority',
        'resolution', 'resolved_at', 'closed_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at'   => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->reference_number)) {
                $ticket->reference_number = static::generateReferenceNumber();
            }
        });
    }

    public static function generateReferenceNumber(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->orderByDesc('id')->value('reference_number');
        $seq  = 1;
        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }
        return 'TKT-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at');
    }

    public function publicReplies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->where('is_internal', false)->orderBy('created_at');
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'support'    => 'Support',
            'billing'    => 'Billing',
            'technical'  => 'Technical',
            'bug_report' => 'Bug Report',
            'other'      => 'Other',
            default      => ucfirst($type),
        };
    }

    public static function typeOptions(): array
    {
        return [
            'support'    => 'Support',
            'billing'    => 'Billing',
            'technical'  => 'Technical',
            'bug_report' => 'Bug Report',
            'other'      => 'Other',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'open'             => 'Open',
            'in_progress'      => 'In Progress',
            'pending_customer' => 'Pending Customer',
            'resolved'         => 'Resolved',
            'closed'           => 'Closed',
        ];
    }

    public static function priorityOptions(): array
    {
        return [
            'low'    => 'Low',
            'medium' => 'Medium',
            'high'   => 'High',
            'urgent' => 'Urgent',
        ];
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'in_progress', 'pending_customer']);
    }
}
```

- [ ] **Step 6: Create `app/Models/TicketReply.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReply extends Model
{
    protected $fillable = [
        'ticket_id', 'user_id', 'body', 'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
```

- [ ] **Step 7: Run migrations**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan migrate
```

- [ ] **Step 8: Run the tests — expect ALL PASS**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/TicketSystemTest.php
```

Expected: 9 tests pass.

Note: `test_customer_can_view_their_tickets`, `test_customer_cannot_view_another_customers_ticket`, and `test_customer_can_create_ticket_via_form` will FAIL until Task 3 routes/views are added — that is expected and acceptable. The other 6 should pass.

Actually, WAIT — rewrite the test file to only include tests that can pass without routes. Move the HTTP tests to a separate section that can be run after Task 3. Here is the adjustment: only run tasks 1-6 in Step 8 of Task 1. The HTTP tests will be added in Task 3.

**Revised test file — split the tests:**

Keep ALL tests in the file as written above. In Step 8, run them with `--filter="test_tickets_table_exists|test_ticket_replies_table_exists|test_ticket_can_be_created|test_ticket_reply_can_be_added|test_ticket_reference_number|test_manage_tickets|test_support_has"`:

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/TicketSystemTest.php --filter="test_tickets_table_exists|test_ticket_replies_table_exists|test_ticket_can_be_created_by_customer|test_ticket_reply_can_be_added|test_ticket_reference_number_increments|test_manage_tickets_permission_exists|test_support_has_manage_tickets_permission"
```

Expected: 7 tests pass (HTTP tests will be skipped for now).

- [ ] **Step 9: Run full suite — no regressions**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 70 tests pass (63 + 7).

- [ ] **Step 10: Commit**

```
git add database/migrations/2026_06_13_240000_create_tickets_table.php database/migrations/2026_06_13_241000_create_ticket_replies_table.php app/Models/Ticket.php app/Models/TicketReply.php tests/Feature/TicketSystemTest.php
git commit -m "feat: add tickets and ticket_replies tables, Ticket/TicketReply models, and system tests"
```

---

## Task 2: Filament TicketResource (Admin Panel)

**Files:**
- Create: `app/Filament/Resources/TicketResource.php`
- Create: `app/Filament/Resources/TicketResource/Pages/ListTickets.php`
- Create: `app/Filament/Resources/TicketResource/Pages/ViewTicket.php`
- Create: `app/Filament/Resources/TicketResource/Pages/CreateTicket.php`

Admins and support staff manage all tickets here. They can see all tickets, filter by status/type/priority, view the full thread, add replies (with an internal-note toggle), change status, and assign to a staff member. There is no separate EditTicket page — status, assignee, and replies are all managed from the ViewTicket page via header actions + infolist entry.

- [ ] **Step 1: Create `app/Filament/Resources/TicketResource.php`**

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Support';
    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'support']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Ticket Details')->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Customer')
                    ->options(User::role('customer')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                Forms\Components\Select::make('assigned_to')
                    ->label('Assigned To')
                    ->options(
                        User::whereHas('roles', fn ($q) =>
                            $q->whereIn('name', ['super_admin', 'admin', 'support'])
                        )->orderBy('name')->pluck('name', 'id')
                    )
                    ->searchable()
                    ->nullable(),

                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Select::make('type')
                    ->options(Ticket::typeOptions())
                    ->default('support')
                    ->required(),

                Forms\Components\Select::make('priority')
                    ->options(Ticket::priorityOptions())
                    ->default('medium')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options(Ticket::statusOptions())
                    ->default('open')
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('resolution')
                    ->rows(3)
                    ->columnSpanFull()
                    ->nullable(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Ref')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Ticket::typeLabel($state))
                    ->color(fn ($state) => match ($state) {
                        'billing'    => 'warning',
                        'technical'  => 'info',
                        'bug_report' => 'danger',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        'low'    => 'gray',
                        default  => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'open'             => 'danger',
                        'in_progress'      => 'warning',
                        'pending_customer' => 'info',
                        'resolved'         => 'success',
                        'closed'           => 'gray',
                        default            => 'gray',
                    }),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Opened')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Ticket::statusOptions()),
                Tables\Filters\SelectFilter::make('type')
                    ->options(Ticket::typeOptions()),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(Ticket::priorityOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Ticket')->schema([
                Infolists\Components\TextEntry::make('reference_number')->label('Reference')->fontFamily('mono'),
                Infolists\Components\TextEntry::make('customer.name')->label('Customer'),
                Infolists\Components\TextEntry::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Ticket::typeLabel($state)),
                Infolists\Components\TextEntry::make('priority')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'urgent' => 'danger', 'high' => 'warning',
                        'medium' => 'info', 'low' => 'gray', default => 'gray',
                    }),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'open' => 'danger', 'in_progress' => 'warning',
                        'pending_customer' => 'info', 'resolved' => 'success',
                        'closed' => 'gray', default => 'gray',
                    }),
                Infolists\Components\TextEntry::make('assignee.name')->label('Assigned To')->placeholder('Unassigned'),
                Infolists\Components\TextEntry::make('subject')->columnSpanFull(),
                Infolists\Components\TextEntry::make('description')->columnSpanFull(),
                Infolists\Components\TextEntry::make('resolution')->placeholder('No resolution noted')->columnSpanFull(),
            ])->columns(3),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view'   => Pages\ViewTicket::route('/{record}'),
        ];
    }
}
```

- [ ] **Step 2: Create `app/Filament/Resources/TicketResource/Pages/ListTickets.php`**

```php
<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
```

- [ ] **Step 3: Create `app/Filament/Resources/TicketResource/Pages/CreateTicket.php`**

```php
<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

- [ ] **Step 4: Create `app/Filament/Resources/TicketResource/Pages/ViewTicket.php`**

The view page shows the infolist + thread of replies + header actions for status changes and replying.

```php
<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reply')
                ->label('Add Reply')
                ->icon('heroicon-o-chat-bubble-left')
                ->color('primary')
                ->form([
                    Forms\Components\Textarea::make('body')
                        ->label('Reply')
                        ->required()
                        ->rows(4),
                    Forms\Components\Toggle::make('is_internal')
                        ->label('Internal note (not visible to customer)')
                        ->default(false),
                ])
                ->action(function (array $data): void {
                    TicketReply::create([
                        'ticket_id'   => $this->record->id,
                        'user_id'     => auth()->id(),
                        'body'        => $data['body'],
                        'is_internal' => $data['is_internal'],
                    ]);
                    if ($this->record->status === 'open') {
                        $this->record->update(['status' => 'in_progress']);
                    }
                    Notification::make()->title('Reply added')->success()->send();
                }),

            Actions\Action::make('change_status')
                ->label('Change Status')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('status')
                        ->options(Ticket::statusOptions())
                        ->default(fn () => $this->record->status)
                        ->required(),
                    Forms\Components\Select::make('assigned_to')
                        ->label('Assigned To')
                        ->options(
                            User::whereHas('roles', fn ($q) =>
                                $q->whereIn('name', ['super_admin', 'admin', 'support'])
                            )->orderBy('name')->pluck('name', 'id')
                        )
                        ->nullable()
                        ->default(fn () => $this->record->assigned_to),
                    Forms\Components\Textarea::make('resolution')
                        ->rows(3)
                        ->nullable(),
                ])
                ->action(function (array $data): void {
                    $updates = ['status' => $data['status'], 'assigned_to' => $data['assigned_to']];
                    if (!empty($data['resolution'])) {
                        $updates['resolution'] = $data['resolution'];
                    }
                    if (in_array($data['status'], ['resolved', 'closed']) && !$this->record->resolved_at) {
                        $updates['resolved_at'] = now();
                    }
                    if ($data['status'] === 'closed' && !$this->record->closed_at) {
                        $updates['closed_at'] = now();
                    }
                    $this->record->update($updates);
                    Notification::make()->title('Ticket updated')->success()->send();
                    $this->refreshFormData(['status', 'assigned_to', 'resolution']);
                }),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return $this->record->reference_number . ' — ' . $this->record->subject;
    }
}
```

- [ ] **Step 5: Run full test suite — no regressions**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 70 tests pass.

- [ ] **Step 6: Commit**

```
git add app/Filament/Resources/TicketResource.php app/Filament/Resources/TicketResource/
git commit -m "feat: add Filament TicketResource for admin/support ticket management"
```

---

## Task 3: Customer Portal — Support Tickets

**Files:**
- Create: `app/Http/Controllers/Customer/TicketController.php`
- Create: `resources/views/customer/tickets/index.blade.php`
- Create: `resources/views/customer/tickets/create.blade.php`
- Create: `resources/views/customer/tickets/show.blade.php`
- Modify: `routes/web.php` — add ticket routes to customer group
- Modify: `resources/views/components/layouts/customer.blade.php` — replace "Support" span with link

- [ ] **Step 1: Create `app/Http/Controllers/Customer/TicketController.php`**

```php
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $tickets = Ticket::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('customer.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('customer.tickets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'type'        => 'required|in:support,billing,technical,bug_report,other',
            'priority'    => 'required|in:low,medium,high,urgent',
        ]);

        $user   = Auth::user();
        Ticket::create(array_merge($validated, [
            'user_id' => $user->id,
            'status'  => 'open',
        ]));

        return redirect()
            ->route('customer.tickets', ['locale' => app()->getLocale()])
            ->with('success', 'Your support ticket has been submitted. We\'ll be in touch shortly.');
    }

    public function show(Request $request)
    {
        $user   = Auth::user();
        $id     = (int) $request->route('id');
        $ticket = Ticket::with('publicReplies.author')->findOrFail($id);

        abort_if((int) $ticket->user_id !== $user->id, 403);

        return view('customer.tickets.show', compact('ticket'));
    }

    public function reply(Request $request)
    {
        $user   = Auth::user();
        $id     = (int) $request->route('id');
        $ticket = Ticket::findOrFail($id);

        abort_if((int) $ticket->user_id !== $user->id, 403);
        abort_unless($ticket->isOpen(), 403, 'This ticket is closed.');

        $validated = $request->validate([
            'body' => 'required|string|max:10000',
        ]);

        TicketReply::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'body'        => $validated['body'],
            'is_internal' => false,
        ]);

        if ($ticket->status === 'pending_customer') {
            $ticket->update(['status' => 'in_progress']);
        }

        return redirect()
            ->route('customer.tickets.show', ['locale' => app()->getLocale(), 'id' => $ticket->id])
            ->with('success', 'Your reply has been added.');
    }
}
```

- [ ] **Step 2: Add routes to `routes/web.php`**

Inside the customer group, after the licenses routes, add:

```php
                Route::get('/tickets',           [\App\Http\Controllers\Customer\TicketController::class, 'index'])->name('tickets');
                Route::get('/tickets/create',    [\App\Http\Controllers\Customer\TicketController::class, 'create'])->name('tickets.create');
                Route::post('/tickets',          [\App\Http\Controllers\Customer\TicketController::class, 'store'])->name('tickets.store');
                Route::get('/tickets/{id}',      [\App\Http\Controllers\Customer\TicketController::class, 'show'])->name('tickets.show');
                Route::post('/tickets/{id}/reply', [\App\Http\Controllers\Customer\TicketController::class, 'reply'])->name('tickets.reply');
```

**IMPORTANT:** The `/tickets/create` GET route MUST be registered BEFORE `/tickets/{id}` GET route, otherwise `create` will be consumed as an `{id}` parameter.

- [ ] **Step 3: Replace "Support" nav link in customer layout**

In `resources/views/components/layouts/customer.blade.php`, find:

```html
            <span class="cp-nav-link cp-nav-link-disabled" title="Coming soon">
                <i data-lucide="ticket" style="width:16px;height:16px"></i> Support
            </span>
```

Replace with:

```html
            <a href="{{ route('customer.tickets', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.tickets*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="ticket" style="width:16px;height:16px"></i> Support
            </a>
```

- [ ] **Step 4: Create `resources/views/customer/tickets/index.blade.php`**

```html
<x-layouts.customer title="Support Tickets">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">Support Tickets</h1>
            <p class="cp-page-subtitle">Track your support requests</p>
        </div>
        <a href="{{ route('customer.tickets.create', ['locale' => app()->getLocale()]) }}"
           class="cp-btn-primary">+ New Ticket</a>
    </div>

    @if (session('success'))
        <div class="cp-flash-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    @if($tickets->isEmpty())
        <div class="cp-section-card" style="text-align:center;padding:3rem;">
            <div class="cp-empty-state">
                <i data-lucide="ticket" style="width:48px;height:48px;color:#334155"></i>
                <p>No support tickets yet.</p>
                <p style="font-size:0.8125rem">Submit a ticket if you need help and we'll get back to you.</p>
                <a href="{{ route('customer.tickets.create', ['locale' => app()->getLocale()]) }}"
                   class="cp-btn-primary" style="display:inline-block;margin-top:1rem;">Open a Ticket</a>
            </div>
        </div>
    @else
        <div class="cp-section-card" style="padding:0;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #334155;">
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Ref</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Subject</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Type</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Priority</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Opened</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    @php
                        $statusColor = match($ticket->status) {
                            'open'             => '#ef4444',
                            'in_progress'      => '#eab308',
                            'pending_customer' => '#3b82f6',
                            'resolved'         => '#00C896',
                            'closed'           => '#64748b',
                            default            => '#94a3b8',
                        };
                        $priorityColor = match($ticket->priority) {
                            'urgent' => '#ef4444',
                            'high'   => '#f97316',
                            'medium' => '#3b82f6',
                            'low'    => '#64748b',
                            default  => '#94a3b8',
                        };
                    @endphp
                    <tr style="border-bottom:1px solid #1e293b;">
                        <td style="padding:0.75rem;">
                            <span style="color:#00C896;font-family:monospace;font-size:0.8rem;">{{ $ticket->reference_number }}</span>
                        </td>
                        <td style="padding:0.75rem;">
                            <span style="color:#e2e8f0;font-size:0.875rem;">{{ Str::limit($ticket->subject, 45) }}</span>
                        </td>
                        <td style="padding:0.75rem;">
                            <span style="color:#94a3b8;font-size:0.8125rem;">{{ \App\Models\Ticket::typeLabel($ticket->type) }}</span>
                        </td>
                        <td style="padding:0.75rem;">
                            <span style="color:{{ $priorityColor }};font-size:0.8125rem;font-weight:600;text-transform:capitalize;">{{ $ticket->priority }}</span>
                        </td>
                        <td style="padding:0.75rem;">
                            <span style="color:{{ $statusColor }};font-size:0.8125rem;font-weight:600;">
                                {{ \App\Models\Ticket::statusOptions()[$ticket->status] ?? $ticket->status }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;color:#64748b;font-size:0.8125rem;">{{ $ticket->created_at->diffForHumans() }}</td>
                        <td style="padding:0.75rem;text-align:right;">
                            <a href="{{ route('customer.tickets.show', ['locale' => app()->getLocale(), 'id' => $ticket->id]) }}"
                               class="cp-btn-outline" style="font-size:0.75rem;padding:0.375rem 0.75rem;">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:1rem 0.75rem 0;">
                {{ $tickets->links() }}
            </div>
        </div>
    @endif
</x-layouts.customer>
```

- [ ] **Step 5: Create `resources/views/customer/tickets/create.blade.php`**

```html
<x-layouts.customer title="New Support Ticket">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">New Support Ticket</h1>
            <p class="cp-page-subtitle">Describe your issue and we'll get back to you</p>
        </div>
        <a href="{{ route('customer.tickets', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">&larr; Back</a>
    </div>

    <div class="cp-section-card">
        <form method="POST" action="{{ route('customer.tickets.store', ['locale' => app()->getLocale()]) }}">
            @csrf

            <div style="margin-bottom:1.25rem;">
                <label style="display:block;color:#94a3b8;font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Subject *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                    style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;"
                    placeholder="Brief description of your issue">
                @error('subject')
                    <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
                <div>
                    <label style="display:block;color:#94a3b8;font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Category *</label>
                    <select name="type" required
                        style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;">
                        @foreach(\App\Models\Ticket::typeOptions() as $value => $label)
                            <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label style="display:block;color:#94a3b8;font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Priority *</label>
                    <select name="priority" required
                        style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;">
                        @foreach(\App\Models\Ticket::priorityOptions() as $value => $label)
                            <option value="{{ $value }}" {{ old('priority', 'medium') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('priority')
                        <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block;color:#94a3b8;font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Description *</label>
                <textarea name="description" required maxlength="10000" rows="7"
                    style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;resize:vertical;"
                    placeholder="Please provide as much detail as possible...">{{ old('description') }}</textarea>
                @error('description')
                    <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display:flex;gap:0.75rem;">
                <button type="submit" class="cp-btn-primary">Submit Ticket</button>
                <a href="{{ route('customer.tickets', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.customer>
```

- [ ] **Step 6: Create `resources/views/customer/tickets/show.blade.php`**

```html
<x-layouts.customer title="{{ $ticket->reference_number }}">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">{{ $ticket->subject }}</h1>
            <p class="cp-page-subtitle">
                <span style="font-family:monospace;color:#00C896;">{{ $ticket->reference_number }}</span>
                &middot; Opened {{ $ticket->created_at->diffForHumans() }}
            </p>
        </div>
        <a href="{{ route('customer.tickets', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">&larr; Back</a>
    </div>

    @php
        $statusColor = match($ticket->status) {
            'open'             => '#ef4444',
            'in_progress'      => '#eab308',
            'pending_customer' => '#3b82f6',
            'resolved'         => '#00C896',
            'closed'           => '#64748b',
            default            => '#94a3b8',
        };
    @endphp

    @if (session('success'))
        <div class="cp-flash-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    {{-- Status badge --}}
    <div style="display:flex;gap:0.75rem;align-items:center;margin-bottom:1.5rem;">
        <span style="color:{{ $statusColor }};font-weight:700;font-size:0.875rem;text-transform:uppercase;letter-spacing:0.05em;">
            {{ \App\Models\Ticket::statusOptions()[$ticket->status] ?? $ticket->status }}
        </span>
        <span style="color:#64748b;font-size:0.8rem;">&#183;</span>
        <span style="color:#94a3b8;font-size:0.8125rem;">{{ \App\Models\Ticket::typeLabel($ticket->type) }}</span>
        <span style="color:#64748b;font-size:0.8rem;">&#183;</span>
        <span style="color:#94a3b8;font-size:0.8125rem;text-transform:capitalize;">{{ $ticket->priority }} priority</span>
    </div>

    {{-- Original message --}}
    <div class="cp-section-card" style="margin-bottom:1rem;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.75rem;">
            <span style="color:#e2e8f0;font-weight:600;font-size:0.875rem;">Original Request</span>
            <span style="color:#64748b;font-size:0.75rem;">{{ $ticket->created_at->format('d M Y, H:i') }}</span>
        </div>
        <p style="color:#94a3b8;font-size:0.875rem;line-height:1.7;white-space:pre-wrap;">{{ $ticket->description }}</p>
    </div>

    {{-- Reply thread --}}
    @foreach($ticket->publicReplies as $reply)
    @php
        $isStaff = $reply->author?->hasAnyRole(['super_admin', 'admin', 'support']);
        $bgColor = $isStaff ? '#0f172a' : '#1e293b';
        $borderColor = $isStaff ? '#00C896' : '#334155';
        $authorLabel = $isStaff ? 'OPES Support' : ($reply->author?->name ?? 'Unknown');
    @endphp
    <div style="background:{{ $bgColor }};border:1px solid {{ $borderColor }};border-radius:10px;padding:1rem 1.25rem;margin-bottom:0.75rem;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.75rem;">
            <span style="color:{{ $isStaff ? '#00C896' : '#e2e8f0' }};font-weight:600;font-size:0.875rem;">{{ $authorLabel }}</span>
            <span style="color:#64748b;font-size:0.75rem;">{{ $reply->created_at->format('d M Y, H:i') }}</span>
        </div>
        <p style="color:#94a3b8;font-size:0.875rem;line-height:1.7;white-space:pre-wrap;">{{ $reply->body }}</p>
    </div>
    @endforeach

    {{-- Resolution --}}
    @if($ticket->resolution)
    <div style="background:rgba(0,200,150,0.06);border:1px solid rgba(0,200,150,0.2);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1rem;">
        <p style="color:#00C896;font-weight:600;font-size:0.875rem;margin-bottom:0.5rem;">&#10003; Resolution</p>
        <p style="color:#94a3b8;font-size:0.875rem;line-height:1.7;">{{ $ticket->resolution }}</p>
    </div>
    @endif

    {{-- Reply form --}}
    @if($ticket->isOpen())
    <div class="cp-section-card" style="margin-top:1.25rem;">
        <h3 style="color:#e2e8f0;font-size:0.9rem;font-weight:600;margin-bottom:1rem;">Add a Reply</h3>
        <form method="POST" action="{{ route('customer.tickets.reply', ['locale' => app()->getLocale(), 'id' => $ticket->id]) }}">
            @csrf
            <textarea name="body" required maxlength="10000" rows="5"
                style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;resize:vertical;margin-bottom:0.75rem;"
                placeholder="Type your reply...">{{ old('body') }}</textarea>
            @error('body')
                <p style="color:#ef4444;font-size:0.75rem;margin-bottom:0.5rem;">{{ $message }}</p>
            @enderror
            <button type="submit" class="cp-btn-primary">Send Reply</button>
        </form>
    </div>
    @else
    <div style="text-align:center;padding:1.5rem;color:#64748b;font-size:0.875rem;">
        This ticket is {{ $ticket->status }}. <a href="{{ route('customer.tickets.create', ['locale' => app()->getLocale()]) }}" style="color:#00C896;">Open a new ticket</a> if you need further assistance.
    </div>
    @endif
</x-layouts.customer>
```

- [ ] **Step 7: Run all tests — expect 72 passing**

```
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 72 tests pass (70 + 2 HTTP customer tests now passing with routes live).

- [ ] **Step 8: Commit**

```
git add app/Http/Controllers/Customer/TicketController.php resources/views/customer/tickets/ routes/web.php resources/views/components/layouts/customer.blade.php
git commit -m "feat: add customer portal Support Tickets with threaded replies"
```

---

## Self-Review

### 1. Spec coverage

| Requirement | Covered |
|---|---|
| `tickets` table with all columns | ✅ Task 1 migration |
| `ticket_replies` table | ✅ Task 1 migration |
| Reference number auto-generation `TKT-2026-00001` | ✅ Task 1 — `booted()` hook |
| `Ticket` model with relationships, helpers, status logic | ✅ Task 1 |
| `TicketReply` model with ticket + author relations | ✅ Task 1 |
| Filament TicketResource — list, create, view | ✅ Task 2 |
| Admin can reply with internal-note toggle | ✅ Task 2 ViewTicket header action |
| Admin can change status + assign + resolution | ✅ Task 2 ViewTicket header action |
| Customer portal — list, create, show | ✅ Task 3 |
| Customer can reply to open tickets | ✅ Task 3 TicketController::reply() |
| Customer isolation | ✅ Task 3 — abort_if(user_id mismatch) |
| "Support" nav link activated | ✅ Task 3 |
| `manage_tickets` permission exists | ✅ Already in seeder, tested in Task 1 |

### 2. Placeholder scan

None — all steps have actual code.

### 3. Route ordering concern

`/tickets/create` (GET) is registered BEFORE `/tickets/{id}` (GET) — correct, avoids `create` being consumed as an `{id}` value.

### 4. Internal replies hidden from customer

`publicReplies()` scope filters `is_internal = false`. The customer `show` view loads `$ticket->publicReplies` (eager-loaded in controller). Staff-only notes are never exposed to the customer view.
