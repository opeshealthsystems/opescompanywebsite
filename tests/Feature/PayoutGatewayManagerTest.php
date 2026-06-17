<?php

namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use App\Services\Payouts\ManualPayoutGateway;
use App\Services\Payouts\MtnMomoPayoutGateway;
use App\Services\Payouts\OrangeMoneyPayoutGateway;
use App\Services\Payouts\PayoutGatewayManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutGatewayManagerTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): PayoutGatewayManager
    {
        return app(PayoutGatewayManager::class);
    }

    private function applicationFor(?string $number): PractitionerApplication
    {
        $user = User::factory()->create();
        $user->practitionerProfile()->create(array_filter([
            'profession'        => 'doctor',
            'workplace_country' => 'CM',
            'payout_number'     => $number,
        ], fn ($v) => $v !== null));
        $program = PractitionerProgram::factory()->paid()->create();

        return PractitionerApplication::factory()->create([
            'practitioner_id' => $user->id,
            'program_id'      => $program->id,
        ]);
    }

    public function test_driver_for_network(): void
    {
        $this->assertInstanceOf(MtnMomoPayoutGateway::class, $this->manager()->driverFor('mtn'));
        $this->assertInstanceOf(OrangeMoneyPayoutGateway::class, $this->manager()->driverFor('orange'));
        $this->assertInstanceOf(ManualPayoutGateway::class, $this->manager()->driverFor('manual'));
        $this->assertInstanceOf(ManualPayoutGateway::class, $this->manager()->driverFor(null));
    }

    public function test_resolve_network_from_profile_number(): void
    {
        $app = $this->applicationFor('677123456');
        $this->assertSame('mtn', $this->manager()->resolveNetwork($app));
    }

    public function test_resolve_network_override_wins(): void
    {
        $app = $this->applicationFor('677123456');
        $this->assertSame('orange', $this->manager()->resolveNetwork($app, 'orange'));
    }

    public function test_resolve_network_falls_back_to_manual_without_number(): void
    {
        $app = $this->applicationFor(null);
        $this->assertSame('manual', $this->manager()->resolveNetwork($app));
    }
}
