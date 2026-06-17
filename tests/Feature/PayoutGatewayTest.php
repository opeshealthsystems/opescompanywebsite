<?php

namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use App\Services\Payouts\ManualPayoutGateway;
use App\Services\Payouts\MtnMomoPayoutGateway;
use App\Services\Payouts\PayoutGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutGatewayTest extends TestCase
{
    use RefreshDatabase;

    private function paidApplication(): PractitionerApplication
    {
        $program = PractitionerProgram::factory()->paid()->create();

        return PractitionerApplication::factory()->create([
            'practitioner_id' => User::factory()->create()->id,
            'program_id'      => $program->id,
            'status'          => 'approved',
            'payout_status'   => 'pending',
        ]);
    }

    public function test_container_resolves_manual_driver_by_default(): void
    {
        config(['payouts.driver' => 'manual']);
        $this->assertInstanceOf(ManualPayoutGateway::class, app(PayoutGateway::class));
    }

    public function test_container_resolves_configured_provider_driver(): void
    {
        config(['payouts.driver' => 'mtn_momo']);
        $this->assertInstanceOf(MtnMomoPayoutGateway::class, app(PayoutGateway::class));
    }

    public function test_manual_gateway_settles_payout_on_the_application(): void
    {
        $application = $this->paidApplication();

        $result = (new ManualPayoutGateway())->disburse($application, 50000, 'XAF', ['reference' => 'MOMO-9001']);

        $this->assertTrue($result->success);
        $this->assertSame('paid', $result->status);

        $fresh = $application->fresh();
        $this->assertSame('paid', $fresh->payout_status);
        $this->assertSame('50000.00', (string) $fresh->payout_amount);
        $this->assertSame('XAF', $fresh->payout_currency);
        $this->assertSame('MOMO-9001', $fresh->payout_reference);
        $this->assertNotNull($fresh->paid_at);
    }

    public function test_unimplemented_provider_driver_throws_clearly(): void
    {
        $application = $this->paidApplication();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('not yet implemented');

        (new MtnMomoPayoutGateway())->disburse($application, 50000, 'XAF');
    }
}
