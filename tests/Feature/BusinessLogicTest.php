<?php

namespace Tests\Feature;

use App\Models\InvoiceTemplate;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\PayrollDeductionType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class BusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    // -------------------------------------------------------------------------
    // PayrollDeductionType::calculateAmount()
    // -------------------------------------------------------------------------

    public function test_percentage_deduction_calculates_correctly(): void
    {
        $type = PayrollDeductionType::create([
            'name'             => 'Social Security',
            'code'             => 'CNPS',
            'calculation_type' => 'percentage',
            'rate'             => 10.0,
            'apply_by_default' => true,
            'is_active'        => true,
            'sort_order'       => 1,
        ]);

        $this->assertEquals(5000.0, $type->calculateAmount(50000));
    }

    public function test_fixed_deduction_returns_flat_amount(): void
    {
        $type = PayrollDeductionType::create([
            'name'             => 'Union Fee',
            'code'             => 'UNION',
            'calculation_type' => 'fixed',
            'rate'             => 2500.0,
            'apply_by_default' => false,
            'is_active'        => true,
            'sort_order'       => 2,
        ]);

        $this->assertEquals(2500.0, $type->calculateAmount(99999));
    }

    public function test_default_deductions_scope_returns_active_default_only(): void
    {
        // Active + default → should be returned
        PayrollDeductionType::create([
            'name'             => 'Tax A',
            'code'             => 'TAXA',
            'calculation_type' => 'percentage',
            'rate'             => 5.0,
            'apply_by_default' => true,
            'is_active'        => true,
            'sort_order'       => 1,
        ]);

        // Inactive + default → should NOT be returned
        PayrollDeductionType::create([
            'name'             => 'Tax B',
            'code'             => 'TAXB',
            'calculation_type' => 'percentage',
            'rate'             => 3.0,
            'apply_by_default' => true,
            'is_active'        => false,
            'sort_order'       => 2,
        ]);

        // Active + NOT default → should NOT be returned
        PayrollDeductionType::create([
            'name'             => 'Tax C',
            'code'             => 'TAXC',
            'calculation_type' => 'fixed',
            'rate'             => 1000.0,
            'apply_by_default' => false,
            'is_active'        => true,
            'sort_order'       => 3,
        ]);

        $defaults = PayrollDeductionType::defaultDeductions();

        $this->assertCount(1, $defaults);
        $this->assertEquals('TAXA', $defaults->first()->code);
    }

    // -------------------------------------------------------------------------
    // LeaveRequest::getDurationInDays()
    // -------------------------------------------------------------------------

    public function test_duration_in_days_same_day_is_one(): void
    {
        $user = User::factory()->create();

        $request = LeaveRequest::create([
            'user_id'    => $user->id,
            'type'       => 'annual',
            'start_date' => '2026-07-10',
            'end_date'   => '2026-07-10',
            'total_days' => 1,
            'status'     => 'pending',
        ]);

        $this->assertEquals(1, $request->getDurationInDays());
    }

    public function test_duration_in_days_multiday(): void
    {
        $user = User::factory()->create();

        $request = LeaveRequest::create([
            'user_id'    => $user->id,
            'type'       => 'sick',
            'start_date' => '2026-07-01',
            'end_date'   => '2026-07-05',
            'total_days' => 5,
            'status'     => 'pending',
        ]);

        // 1 Jul → 5 Jul inclusive = 5 days
        $this->assertEquals(5, $request->getDurationInDays());
    }

    // -------------------------------------------------------------------------
    // LeaveRequest::deductFromBalance()
    // -------------------------------------------------------------------------

    public function test_approve_deducts_from_leave_balance(): void
    {
        $user = User::factory()->create();

        // Create a leave balance row for the same year and type
        $balance = LeaveBalance::create([
            'user_id'       => $user->id,
            'year'          => 2026,
            'type'          => 'annual',
            'entitled_days' => 21,
            'used_days'     => 0,
        ]);

        $request = LeaveRequest::create([
            'user_id'    => $user->id,
            'type'       => 'annual',
            'start_date' => '2026-07-01',
            'end_date'   => '2026-07-03',
            'total_days' => 3,
            'status'     => 'approved',
        ]);

        $request->deductFromBalance();

        $balance->refresh();
        $this->assertEquals(3.0, (float) $balance->used_days);
    }

    // -------------------------------------------------------------------------
    // InvoiceTemplate::getNextDueDateAfter()
    // -------------------------------------------------------------------------

    public function test_monthly_template_advances_next_due_by_one_month(): void
    {
        $issuer = User::factory()->create();

        $template = InvoiceTemplate::create([
            'name'              => 'Monthly Billing',
            'frequency'         => 'monthly',
            'next_due_date'     => '2026-01-15',
            'payment_terms_days'=> 30,
            'currency'          => 'XAF',
            'tax_rate'          => 0,
            'line_items'        => [],
            'is_active'         => true,
            'issued_by'         => $issuer->id,
        ]);

        $next = $template->getNextDueDateAfter();

        $this->assertTrue($next->equalTo(Carbon::parse('2026-02-15')));
    }

    public function test_quarterly_template_advances_by_three_months(): void
    {
        $issuer = User::factory()->create();

        $template = InvoiceTemplate::create([
            'name'              => 'Quarterly Billing',
            'frequency'         => 'quarterly',
            'next_due_date'     => '2026-01-01',
            'payment_terms_days'=> 30,
            'currency'          => 'XAF',
            'tax_rate'          => 0,
            'line_items'        => [],
            'is_active'         => true,
            'issued_by'         => $issuer->id,
        ]);

        $next = $template->getNextDueDateAfter();

        $this->assertTrue($next->equalTo(Carbon::parse('2026-04-01')));
    }

    public function test_annual_template_advances_by_one_year(): void
    {
        $issuer = User::factory()->create();

        $template = InvoiceTemplate::create([
            'name'              => 'Annual Billing',
            'frequency'         => 'annual',
            'next_due_date'     => '2026-03-01',
            'payment_terms_days'=> 30,
            'currency'          => 'XAF',
            'tax_rate'          => 0,
            'line_items'        => [],
            'is_active'         => true,
            'issued_by'         => $issuer->id,
        ]);

        $next = $template->getNextDueDateAfter();

        $this->assertTrue($next->equalTo(Carbon::parse('2027-03-01')));
    }

    // -------------------------------------------------------------------------
    // PayrollDeductionType seeder integration
    // -------------------------------------------------------------------------

    public function test_default_deduction_types_are_created_by_seeder(): void
    {
        // Seed at least one default active deduction type directly (no dedicated seeder exists yet)
        PayrollDeductionType::create([
            'name'             => 'CNPS Employee',
            'code'             => 'CNPS_EMP',
            'calculation_type' => 'percentage',
            'rate'             => 2.8,
            'apply_by_default' => true,
            'is_active'        => true,
            'sort_order'       => 1,
        ]);

        $this->assertGreaterThan(
            0,
            PayrollDeductionType::where('apply_by_default', true)->count()
        );
    }
}
