<?php

namespace Tests\Feature;

use App\Contexts\Workforce\Application\LeaveDecision;
use App\Models\{Department, LeaveBalance, LeaveRequest, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BusinessWorkflowSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        foreach (['manager', 'hr'] as $role) Role::findOrCreate($role, 'web');
    }

    private function leave(User $employee): LeaveRequest
    {
        return LeaveRequest::create(['user_id' => $employee->id, 'type' => 'annual', 'start_date' => '2026-10-01',
            'end_date' => '2026-10-02', 'total_days' => 1.5, 'status' => 'pending']);
    }

    public function test_repeated_approval_deducts_once_and_preserves_fractional_days(): void
    {
        $hr = User::factory()->create(['is_active' => true]); $hr->assignRole('hr');
        $employee = User::factory()->create();
        $balance = LeaveBalance::create(['user_id' => $employee->id, 'type' => 'annual', 'year' => 2026, 'entitled_days' => 20, 'used_days' => 0]);
        $leave = $this->leave($employee);
        app(LeaveDecision::class)->decide($hr, $leave->id, 'approved');
        app(LeaveDecision::class)->decide($hr, $leave->id, 'approved');
        $this->assertSame('1.5', $balance->fresh()->used_days);
        $this->assertSame($hr->id, $leave->fresh()->approved_by);
    }

    public function test_manager_cannot_decide_leave_outside_their_department(): void
    {
        $manager = User::factory()->create(['is_active' => true]); $manager->assignRole('manager');
        Department::create(['name' => 'Managed', 'code' => 'M', 'head_id' => $manager->id]);
        $leave = $this->leave(User::factory()->create());
        try { app(LeaveDecision::class)->decide($manager, $leave->id, 'approved'); $this->fail('Foreign record was accepted'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        $this->assertSame('pending', $leave->fresh()->status);
    }

    public function test_an_approved_request_cannot_be_rejected_without_a_separate_reversal(): void
    {
        $hr = User::factory()->create(['is_active' => true]); $hr->assignRole('hr');
        $leave = $this->leave(User::factory()->create());
        app(LeaveDecision::class)->decide($hr, $leave->id, 'approved');
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(LeaveDecision::class)->decide($hr, $leave->id, 'rejected');
    }

    public function test_invoice_settlement_records_only_the_outstanding_payment_once(): void
    {
        Role::findOrCreate('accountant', 'web');
        $actor = User::factory()->create(['is_active' => true]); $actor->assignRole('accountant');
        $invoice = \App\Models\Invoice::create(['customer_id' => User::factory()->create()->id, 'issued_by' => $actor->id, 'status' => 'sent', 'currency' => 'XAF', 'tax_rate' => 0]);
        $invoice->items()->create(['description' => 'Fictional service', 'quantity' => 1, 'unit_price' => 1000]);
        $service = app(\App\Contexts\Finance\Application\RecordInvoicePayment::class);
        $service->record($actor, $invoice->id, ['amount' => 250, 'payment_method' => 'cash', 'payment_date' => today()->toDateString()]);
        $service->record($actor, $invoice->id, ['payment_method' => 'other', 'payment_date' => today()->toDateString()], true);
        $service->record($actor, $invoice->id, ['payment_method' => 'other', 'payment_date' => today()->toDateString()], true);
        $this->assertDatabaseCount('invoice_payments', 2);
        $this->assertSame(1000.0, $invoice->fresh()->amount_paid);
        $this->assertSame('paid', $invoice->fresh()->status);
    }
}
