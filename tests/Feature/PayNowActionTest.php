<?php

namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use App\Services\Payouts\PayoutGatewayManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayNowActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('payouts.mtn_momo', [
            'base_url' => 'https://sandbox.momodeveloper.mtn.com',
            'subscription_key' => 'k', 'api_user' => 'u', 'api_key' => 'a', 'environment' => 'sandbox',
        ]);
    }

    private function paidApp(): PractitionerApplication
    {
        $user = User::factory()->create();
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM', 'payout_number' => '677123456']);
        $program = PractitionerProgram::factory()->paid()->create();

        return PractitionerApplication::factory()->create([
            'practitioner_id' => $user->id,
            'program_id'      => $program->id,
            'status'          => 'approved',
            'payout_status'   => 'pending',
        ]);
    }

    public function test_pay_now_initiates_via_resolved_driver(): void
    {
        Http::fake([
            '*/disbursement/token/'        => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            '*/disbursement/v1_0/transfer' => Http::response('', 202),
        ]);

        $app = $this->paidApp();
        $manager = app(PayoutGatewayManager::class);
        $network = $manager->resolveNetwork($app);
        $result  = $manager->driverFor($network)->disburse($app, 50000, 'XAF');

        $this->assertSame('mtn', $network);
        $this->assertSame('pending', $result->status);
        $this->assertSame('mtn', $app->fresh()->payout_provider);
    }

    public function test_already_paid_application_is_not_payable(): void
    {
        $app = $this->paidApp();
        $app->update(['payout_status' => 'paid', 'paid_at' => now(), 'payout_amount' => 50000]);

        $this->assertFalse($app->fresh()->isPayable());
    }
}
