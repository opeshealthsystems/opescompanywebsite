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
