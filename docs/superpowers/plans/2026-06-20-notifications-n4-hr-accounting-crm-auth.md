# Notifications N4 — HR + Accounting + CRM + Auth Events Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans (inline). Steps use checkbox (`- [ ]`) syntax.

**Goal:** Wire branded email (+ in-app for logged-in recipients) notifications for the final 8 events — leave submitted/approved/rejected, payment receipt, credit note issued, quote sent, demo-request confirmation, and a branded password reset — onto the N1 spine.

**Architecture:** Status/lifecycle events fire from **model observers** (`#[ObservedBy]`) at the single chokepoint; public submissions fire from controllers as **on-demand mail**. Recipient routing (verified):
- **Leave** → `LeaveRequest` observer: `created` (status `pending`) notifies all `manager` + `hr` users; `updated` to `approved`/`rejected` notifies the leave's `employee` (a `User`). All logged-in → email + in-app.
- **Payment receipt** → `InvoicePayment` observer `created` → the invoice's `customer` (`User`, email + in-app). (Only the Filament "Record Payment" action creates an `InvoicePayment`; the accountant "mark paid" merely flips status and is intentionally not a receipt trigger.)
- **Credit note issued** → `CreditNote` observer (`created` as `issued`, or `updated` to `issued`) → the credit note's `invoice.customer` (`User`).
- **Quote sent** → `Quote` observer `updated` to `sent` → the quote's `lead` (a CRM `Lead`, **email-only on-demand**, no login).
- **Demo confirmation** → `DemoRequestController@submit` → the requester email (on-demand, email-only).
- **Password reset** → override `User::sendPasswordResetNotification()` to send a branded `ResetPasswordNotification` (email-only; the Laravel broker still validates the token).

**Dynamic channel rule** (applicant/lead notifications): `via()` = `$notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail','database']`; display name passed into the constructor.

**Tech Stack:** Laravel 13.8 / PHP 8.3 / Filament v3 / SQLite (tests). PHP `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`. Brand mail theme published (N1). Suite green at 465.

---

## Conventions (verified)

- **Recipients / relations:** `LeaveRequest->employee` (User, FK `user_id`); `Invoice->customer` (User, FK `customer_id`); `InvoicePayment->invoice`; `CreditNote->invoice`; `Quote->lead` (Lead has `name`,`email`).
- **Roles for leave routing:** `User::role(['manager','hr'])->get()` then `Notification::send($staff, ...)`.
- **No factories** for LeaveRequest/Invoice/InvoicePayment/CreditNote/Quote/Lead/DemoRequest — build with `::create([...])`. Verified column sets:
  - `LeaveRequest::create(['user_id','type','start_date','end_date','total_days','status'])`
  - `Invoice::create(['customer_id','issued_by','status','currency','tax_rate','due_date'])` (invoice_number auto)
  - `$invoice->payments()->create(['amount','payment_method','payment_date','recorded_by'])`
  - `Lead::create(['name','email','source','status'])`
  - `Quote::create(['reference','lead_id','title','status','currency','subtotal','tax_rate','tax_amount','total','created_by'])`
  - `CreditNote::create(['reference','invoice_id','reason','status','currency','subtotal','tax_amount','total','created_by'])`
- **CTA routes (`['locale'=>'en', ...]`):** `customer.invoices.show` (param `id`) — "View invoice"; `hr.leave.index` / `manager.leave.index` — role-aware "Review leave"; `home` — "Visit OPES"; `password.reset.form` (param `token`) — reset link.
- **Observer registration:** `#[\Illuminate\Database\Eloquent\Attributes\ObservedBy(\App\Observers\XObserver::class)]` on the model.
- **Tests:** `Notification::fake()`; `assertSentTo($user, X)` for Users, `assertSentOnDemand(X)` for email-only; `Notification::send`/`assertSentTo($collection, X)` for multi-recipient. setUp seeds `RolePermissionSeeder`.
- **Execution discipline:** PowerShell git `-m`; explicit staged paths; `--filter` then full suite before each commit.

---

## Task 1 — Leave notifications (submitted → managers+HR; approved/rejected → employee)

**Files:**
- Create: `app/Notifications/LeaveRequestSubmitted.php`, `app/Notifications/LeaveApproved.php`, `app/Notifications/LeaveRejected.php`
- Create: `app/Observers/LeaveRequestObserver.php`
- Modify: `app/Models/LeaveRequest.php` (`#[ObservedBy]`)
- Test: `tests/Feature/N4LeaveNotificationsTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveApproved;
use App\Notifications\LeaveRejected;
use App\Notifications\LeaveRequestSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N4LeaveNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function leaveData(User $employee, array $overrides = []): array
    {
        return array_merge([
            'user_id'    => $employee->id,
            'type'       => 'annual',
            'start_date' => '2026-07-10',
            'end_date'   => '2026-07-12',
            'total_days' => 3,
            'status'     => 'pending',
        ], $overrides);
    }

    public function test_submitting_leave_notifies_managers_and_hr(): void
    {
        Notification::fake();
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $hr = User::factory()->create();
        $hr->assignRole('hr');
        $employee = User::factory()->create();

        LeaveRequest::create($this->leaveData($employee));

        Notification::assertSentTo($manager, LeaveRequestSubmitted::class);
        Notification::assertSentTo($hr, LeaveRequestSubmitted::class);
    }

    public function test_approving_leave_notifies_the_employee(): void
    {
        Notification::fake();
        $employee = User::factory()->create();
        $leave    = LeaveRequest::create($this->leaveData($employee));

        $leave->update(['status' => 'approved']);

        Notification::assertSentTo($employee, LeaveApproved::class);
    }

    public function test_rejecting_leave_notifies_the_employee(): void
    {
        Notification::fake();
        $employee = User::factory()->create();
        $leave    = LeaveRequest::create($this->leaveData($employee));

        $leave->update(['status' => 'rejected']);

        Notification::assertSentTo($employee, LeaveRejected::class);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=N4LeaveNotificationsTest`
Expected: FAIL — `Class "App\Notifications\LeaveRequestSubmitted" not found`.

- [ ] **Step 3: Create `app/Notifications/LeaveRequestSubmitted.php`**

```php
<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LeaveRequest $leave) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    private function reviewUrl(object $notifiable): string
    {
        return $notifiable->hasRole('hr')
            ? route('hr.leave.index', ['locale' => 'en'])
            : route('manager.leave.index', ['locale' => 'en']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $employee = $this->leave->employee?->name ?? 'An employee';

        return (new MailMessage)
            ->subject('New leave request from ' . $employee)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($employee . ' submitted a ' . $this->leave->type . ' leave request.')
            ->line('Dates: ' . $this->leave->start_date->format('M j, Y') . ' – ' . $this->leave->end_date->format('M j, Y') . ' (' . $this->leave->total_days . ' day(s)).')
            ->action('Review leave', $this->reviewUrl($notifiable));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'hr.leave_submitted',
            'title' => 'New leave request',
            'body'  => ($this->leave->employee?->name ?? 'An employee') . ' requested ' . $this->leave->type . ' leave.',
            'icon'  => 'calendar-days',
            'url'   => $this->reviewUrl($notifiable),
        ];
    }
}
```

- [ ] **Step 4: Create `app/Notifications/LeaveApproved.php`**

```php
<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LeaveRequest $leave) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your leave request was approved')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your ' . $this->leave->type . ' leave request has been approved.')
            ->line('Dates: ' . $this->leave->start_date->format('M j, Y') . ' – ' . $this->leave->end_date->format('M j, Y') . ' (' . $this->leave->total_days . ' day(s)).')
            ->line('Enjoy your time off.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'hr.leave_approved',
            'title' => 'Leave approved',
            'body'  => 'Your ' . $this->leave->type . ' leave was approved.',
            'icon'  => 'calendar-days',
            'url'   => null,
        ];
    }
}
```

- [ ] **Step 5: Create `app/Notifications/LeaveRejected.php`**

```php
<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LeaveRequest $leave) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Your leave request was declined')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your ' . $this->leave->type . ' leave request for ' . $this->leave->start_date->format('M j, Y') . ' – ' . $this->leave->end_date->format('M j, Y') . ' was not approved.');

        if ($this->leave->notes) {
            $mail->line('Note: ' . $this->leave->notes);
        }

        return $mail->line('Please contact your manager or HR for details.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'hr.leave_rejected',
            'title' => 'Leave declined',
            'body'  => 'Your ' . $this->leave->type . ' leave was declined.',
            'icon'  => 'calendar-days',
            'url'   => null,
        ];
    }
}
```

- [ ] **Step 6: Create `app/Observers/LeaveRequestObserver.php`**

```php
<?php

namespace App\Observers;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveApproved;
use App\Notifications\LeaveRejected;
use App\Notifications\LeaveRequestSubmitted;
use Illuminate\Support\Facades\Notification;

class LeaveRequestObserver
{
    public function created(LeaveRequest $leave): void
    {
        if ($leave->status !== 'pending') {
            return;
        }

        $reviewers = User::role(['manager', 'hr'])->get();
        if ($reviewers->isNotEmpty()) {
            Notification::send($reviewers, new LeaveRequestSubmitted($leave));
        }
    }

    public function updated(LeaveRequest $leave): void
    {
        if (! $leave->wasChanged('status')) {
            return;
        }

        $notification = match ($leave->status) {
            'approved' => new LeaveApproved($leave),
            'rejected' => new LeaveRejected($leave),
            default    => null,
        };

        if ($notification) {
            $leave->employee?->notify($notification);
        }
    }
}
```

- [ ] **Step 7: Register the observer**

In `app/Models/LeaveRequest.php`:

```php
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(\App\Observers\LeaveRequestObserver::class)]
class LeaveRequest extends Model
```

- [ ] **Step 8: Run test (expect PASS, 3)** — `--filter=N4LeaveNotificationsTest`
- [ ] **Step 9: Run full suite** — expect 465 + 3 = 468. (Existing `BusinessLogicTest` creates leave requests; with no manager/hr users seeded there, the `created` send has an empty recipient set — harmless.)
- [ ] **Step 10: Commit**

```bash
git add app/Notifications/LeaveRequestSubmitted.php app/Notifications/LeaveApproved.php app/Notifications/LeaveRejected.php app/Observers/LeaveRequestObserver.php app/Models/LeaveRequest.php tests/Feature/N4LeaveNotificationsTest.php
git commit -m "feat(notifications): leave submitted/approved/rejected notifications (N4)"
```

---

## Task 2 — Accounting customer notifications (payment receipt + credit note issued)

**Files:**
- Create: `app/Notifications/PaymentReceived.php`, `app/Notifications/CreditNoteIssued.php`
- Create: `app/Observers/InvoicePaymentObserver.php`, `app/Observers/CreditNoteObserver.php`
- Modify: `app/Models/InvoicePayment.php`, `app/Models/CreditNote.php` (`#[ObservedBy]`)
- Test: `tests/Feature/N4AccountingNotificationsTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\User;
use App\Notifications\CreditNoteIssued;
use App\Notifications\PaymentReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N4AccountingNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function invoiceFor(User $customer): Invoice
    {
        $admin = User::factory()->create();

        return Invoice::create([
            'customer_id' => $customer->id,
            'issued_by'   => $admin->id,
            'status'      => 'sent',
            'currency'    => 'XAF',
            'tax_rate'    => 0,
            'due_date'    => now()->addDays(30)->toDateString(),
        ]);
    }

    public function test_recording_a_payment_notifies_the_customer(): void
    {
        Notification::fake();
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $invoice  = $this->invoiceFor($customer);

        $invoice->payments()->create([
            'amount'         => 50000,
            'payment_method' => 'bank_transfer',
            'payment_date'   => today()->toDateString(),
            'recorded_by'    => User::factory()->create()->id,
        ]);

        Notification::assertSentTo($customer, PaymentReceived::class);
    }

    public function test_issuing_a_credit_note_notifies_the_customer(): void
    {
        Notification::fake();
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $invoice  = $this->invoiceFor($customer);

        CreditNote::create([
            'reference'  => 'CN-2026-0001',
            'invoice_id' => $invoice->id,
            'reason'     => 'Partial refund',
            'status'     => 'issued',
            'currency'   => 'XAF',
            'subtotal'   => 10000,
            'tax_amount' => 0,
            'total'      => 10000,
            'created_by' => User::factory()->create()->id,
        ]);

        Notification::assertSentTo($customer, CreditNoteIssued::class);
    }
}
```

- [ ] **Step 2: Run test to verify it fails** — `--filter=N4AccountingNotificationsTest` → `Class "App\Notifications\PaymentReceived" not found`.

- [ ] **Step 3: Create `app/Notifications/PaymentReceived.php`**

```php
<?php

namespace App\Notifications;

use App\Models\InvoicePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public InvoicePayment $payment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    private function invoiceUrl(): string
    {
        return route('customer.invoices.show', ['locale' => 'en', 'id' => $this->payment->invoice_id]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $invoice = $this->payment->invoice;

        return (new MailMessage)
            ->subject('We received your payment')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We have received your payment of ' . number_format((float) $this->payment->amount) . ' ' . ($invoice?->currency ?? '') . ' for invoice ' . ($invoice?->invoice_number ?? '') . '.')
            ->action('View invoice', $this->invoiceUrl())
            ->line('Thank you for your business.');
    }

    public function toArray(object $notifiable): array
    {
        $invoice = $this->payment->invoice;

        return [
            'type'  => 'accounting.payment_received',
            'title' => 'Payment received',
            'body'  => 'Payment of ' . number_format((float) $this->payment->amount) . ' ' . ($invoice?->currency ?? '') . ' received.',
            'icon'  => 'banknotes',
            'url'   => $this->invoiceUrl(),
        ];
    }
}
```

- [ ] **Step 4: Create `app/Notifications/CreditNoteIssued.php`**

```php
<?php

namespace App\Notifications;

use App\Models\CreditNote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreditNoteIssued extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CreditNote $creditNote) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    private function invoiceUrl(): string
    {
        return route('customer.invoices.show', ['locale' => 'en', 'id' => $this->creditNote->invoice_id]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('A credit note has been issued')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A credit note (' . $this->creditNote->reference . ') of ' . number_format((float) $this->creditNote->total) . ' ' . $this->creditNote->currency . ' has been issued to your account.')
            ->action('View invoice', $this->invoiceUrl())
            ->line('Thank you for your business.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'accounting.credit_note_issued',
            'title' => 'Credit note issued',
            'body'  => 'Credit note ' . $this->creditNote->reference . ' for ' . number_format((float) $this->creditNote->total) . ' ' . $this->creditNote->currency . '.',
            'icon'  => 'receipt-refund',
            'url'   => $this->invoiceUrl(),
        ];
    }
}
```

- [ ] **Step 5: Create `app/Observers/InvoicePaymentObserver.php`**

```php
<?php

namespace App\Observers;

use App\Models\InvoicePayment;
use App\Notifications\PaymentReceived;

class InvoicePaymentObserver
{
    public function created(InvoicePayment $payment): void
    {
        $payment->invoice?->customer?->notify(new PaymentReceived($payment));
    }
}
```

- [ ] **Step 6: Create `app/Observers/CreditNoteObserver.php`**

```php
<?php

namespace App\Observers;

use App\Models\CreditNote;
use App\Notifications\CreditNoteIssued;

class CreditNoteObserver
{
    public function created(CreditNote $creditNote): void
    {
        if ($creditNote->status === 'issued') {
            $this->notifyCustomer($creditNote);
        }
    }

    public function updated(CreditNote $creditNote): void
    {
        if ($creditNote->wasChanged('status') && $creditNote->status === 'issued') {
            $this->notifyCustomer($creditNote);
        }
    }

    private function notifyCustomer(CreditNote $creditNote): void
    {
        $creditNote->invoice?->customer?->notify(new CreditNoteIssued($creditNote));
    }
}
```

- [ ] **Step 7: Register observers**

In `app/Models/InvoicePayment.php`:
```php
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(\App\Observers\InvoicePaymentObserver::class)]
class InvoicePayment extends Model
```

In `app/Models/CreditNote.php`:
```php
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(\App\Observers\CreditNoteObserver::class)]
class CreditNote extends Model
```

- [ ] **Step 8: Run test (expect PASS, 2)** — `--filter=N4AccountingNotificationsTest`
- [ ] **Step 9: Run full suite** — expect 468 + 2 = 470. (Existing `AccountingModuleTest` creates invoices/payments; payment-created now notifies the customer — harmless feed row.)
- [ ] **Step 10: Commit**

```bash
git add app/Notifications/PaymentReceived.php app/Notifications/CreditNoteIssued.php app/Observers/InvoicePaymentObserver.php app/Observers/CreditNoteObserver.php app/Models/InvoicePayment.php app/Models/CreditNote.php tests/Feature/N4AccountingNotificationsTest.php
git commit -m "feat(notifications): payment receipt + credit note issued to customers (N4)"
```

---

## Task 3 — Quote sent (lead) + demo-request confirmation

**Files:**
- Create: `app/Notifications/QuoteSent.php`, `app/Notifications/DemoRequestConfirmation.php`
- Create: `app/Observers/QuoteObserver.php`
- Modify: `app/Models/Quote.php` (`#[ObservedBy]`), `app/Http/Controllers/DemoRequestController.php` (fire confirmation)
- Test: `tests/Feature/N4QuoteDemoNotificationsTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N4QuoteDemoNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_marking_a_quote_sent_emails_the_lead(): void
    {
        Notification::fake();
        $lead  = Lead::create(['name' => 'Jane Buyer', 'email' => 'jane@example.com', 'source' => 'web', 'status' => 'new']);
        $quote = Quote::create([
            'reference'  => 'QTE-2026-0001',
            'lead_id'    => $lead->id,
            'title'      => 'OPES Health OS licence',
            'status'     => 'draft',
            'currency'   => 'XAF',
            'subtotal'   => 100000,
            'tax_rate'   => 0,
            'tax_amount' => 0,
            'total'      => 100000,
            'created_by' => User::factory()->create()->id,
        ]);

        $quote->update(['status' => 'sent']);

        Notification::assertSentOnDemand(\App\Notifications\QuoteSent::class);
    }

    public function test_submitting_a_demo_request_emails_the_requester(): void
    {
        Notification::fake();

        $this->post('/en/book-demo', [
            'name'              => 'Sam Lead',
            'email'             => 'sam@example.com',
            'organization_name' => 'Sam Clinic',
        ])->assertRedirect();

        Notification::assertSentOnDemand(\App\Notifications\DemoRequestConfirmation::class);
    }
}
```

- [ ] **Step 2: Run test to verify it fails** — `--filter=N4QuoteDemoNotificationsTest`.

> If `/en/book-demo` is not the demo route, confirm the demo submit route name in `routes/web.php` (search `book-demo`) and adjust the POST path. The controller is `DemoRequestController@submit`.

- [ ] **Step 3: Create `app/Notifications/QuoteSent.php`**

```php
<?php

namespace App\Notifications;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteSent extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Quote $quote, public string $recipientName) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your quote from OPES Health Systems')
            ->greeting('Hello ' . $this->recipientName . ',')
            ->line('Please find your quote "' . $this->quote->title . '" (' . $this->quote->reference . ').')
            ->line('Total: ' . $this->quote->formatTotal() . ($this->quote->valid_until ? ', valid until ' . $this->quote->valid_until->format('M j, Y') : '') . '.')
            ->line('Our team will follow up with you shortly. Reply to this email with any questions.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'crm.quote_sent',
            'title' => 'Quote sent',
            'body'  => 'Quote ' . $this->quote->reference . ' for ' . $this->quote->formatTotal() . '.',
            'icon'  => 'document-text',
            'url'   => null,
        ];
    }
}
```

- [ ] **Step 4: Create `app/Notifications/DemoRequestConfirmation.php`**

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemoRequestConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $name) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Thanks for requesting a demo')
            ->greeting('Hello ' . $this->name . ',')
            ->line('Thank you for requesting a demo of OPES Health Systems. Our team will be in touch shortly to schedule a time.')
            ->action('Visit OPES', route('home', ['locale' => 'en']))
            ->line('We look forward to showing you what OPES can do.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'crm.demo_request_confirmation',
            'title' => 'Demo request received',
            'body'  => 'We received your demo request and will be in touch.',
            'icon'  => 'calendar',
            'url'   => route('home', ['locale' => 'en']),
        ];
    }
}
```

- [ ] **Step 5: Create `app/Observers/QuoteObserver.php`**

```php
<?php

namespace App\Observers;

use App\Models\Quote;
use App\Notifications\QuoteSent;
use Illuminate\Support\Facades\Notification;

class QuoteObserver
{
    public function updated(Quote $quote): void
    {
        if (! $quote->wasChanged('status') || $quote->status !== 'sent') {
            return;
        }

        $lead = $quote->lead;
        if ($lead && $lead->email) {
            Notification::route('mail', $lead->email)
                ->notify(new QuoteSent($quote, $lead->name ?? 'there'));
        }
    }
}
```

- [ ] **Step 6: Register the Quote observer**

In `app/Models/Quote.php`:
```php
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(\App\Observers\QuoteObserver::class)]
class Quote extends Model
```

- [ ] **Step 7: Fire `DemoRequestConfirmation` from the controller**

In `app/Http/Controllers/DemoRequestController.php` `submit()`, after `$demo = DemoRequest::create(...)` and before the admin `Notification::make()` block:

```php
        \Illuminate\Support\Facades\Notification::route('mail', $demo->email)
            ->notify(new \App\Notifications\DemoRequestConfirmation($demo->name));
```

- [ ] **Step 8: Run test (expect PASS, 2)** — `--filter=N4QuoteDemoNotificationsTest`
- [ ] **Step 9: Run full suite** — expect 470 + 2 = 472.
- [ ] **Step 10: Commit**

```bash
git add app/Notifications/QuoteSent.php app/Notifications/DemoRequestConfirmation.php app/Observers/QuoteObserver.php app/Models/Quote.php app/Http/Controllers/DemoRequestController.php tests/Feature/N4QuoteDemoNotificationsTest.php
git commit -m "feat(notifications): quote sent to lead + demo-request confirmation (N4)"
```

---

## Task 4 — Branded password reset

Replace Laravel's default reset email with an OPES-branded one, keeping the broker's token flow intact.

**Files:**
- Create: `app/Notifications/ResetPasswordNotification.php`
- Modify: `app/Models/User.php` (override `sendPasswordResetNotification`)
- Test: `tests/Feature/N4PasswordResetNotificationTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class N4PasswordResetNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_sends_the_branded_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $user->sendPasswordResetNotification('test-token');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_branded_reset_email_renders_with_brand_and_link(): void
    {
        $user = User::factory()->create(['email' => 'reset@example.com']);
        $html = (new ResetPasswordNotification('tok123'))->toMail($user)->render();

        $this->assertStringContainsString('OPES Health Systems', $html);
        $this->assertStringContainsString('reset-password/tok123', $html);
    }
}
```

- [ ] **Step 2: Run test to verify it fails** — `--filter=N4PasswordResetNotificationTest` → `Class "App\Notifications\ResetPasswordNotification" not found`.

- [ ] **Step 3: Create `app/Notifications/ResetPasswordNotification.php`**

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset.form', ['locale' => 'en', 'token' => $this->token])
            . '?email=' . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Reset your OPES password')
            ->greeting('Hello,')
            ->line('You are receiving this email because we received a password reset request for your OPES Health Systems account.')
            ->action('Reset password', $url)
            ->line('This password reset link will expire shortly. If you did not request a password reset, no further action is required.');
    }
}
```

- [ ] **Step 4: Override the sender on `app/Models/User.php`**

Add `use App\Notifications\ResetPasswordNotification;` to the imports, then add this method to the `User` class (anywhere among its methods):

```php
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
```

- [ ] **Step 5: Run test (expect PASS, 2)** — `--filter=N4PasswordResetNotificationTest`
- [ ] **Step 6: Run full suite** — expect 472 + 2 = 474.
- [ ] **Step 7: Commit**

```bash
git add app/Notifications/ResetPasswordNotification.php app/Models/User.php tests/Feature/N4PasswordResetNotificationTest.php
git commit -m "feat(notifications): branded OPES password reset email (N4)"
```

---

## Final verification (N4)

1. **Full suite green:** expect 465 prior + 11 new ≈ 476, 0 failures.
2. **Channel correctness:** leave/payment/credit-note go to logged-in users (email + feed); quote/demo go to leads (email only); password reset is email only and the broker token flow is unchanged.
3. **Then:** finishing-a-development-branch → merge `feat/notifications-n4` to main (verify suite on merged main). N2–N4 complete; update memory and report.
