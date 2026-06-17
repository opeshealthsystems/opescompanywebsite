<?php

namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use App\Services\Payouts\MtnMomoPayoutGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MtnMomoPayoutGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('payouts.mtn_momo', [
            'base_url'         => 'https://sandbox.momodeveloper.mtn.com',
            'subscription_key' => 'test-sub-key',
            'api_user'         => 'test-api-user',
            'api_key'          => 'test-api-key',
            'environment'      => 'sandbox',
        ]);
    }

    private function paidApplication(string $number = '677123456'): PractitionerApplication
    {
        $user = User::factory()->create();
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM', 'payout_number' => $number]);
        $program = PractitionerProgram::factory()->paid()->create();

        return PractitionerApplication::factory()->create([
            'practitioner_id' => $user->id,
            'program_id'      => $program->id,
            'status'          => 'approved',
            'payout_status'   => 'pending',
        ]);
    }

    public function test_disburse_initiates_transfer_and_marks_pending(): void
    {
        Http::fake([
            '*/disbursement/token/'        => Http::response(['access_token' => 'tok-123', 'expires_in' => 3600], 200),
            '*/disbursement/v1_0/transfer' => Http::response('', 202),
        ]);

        $app = $this->paidApplication();
        $result = (new MtnMomoPayoutGateway())->disburse($app, 50000, 'XAF');

        $this->assertTrue($result->success);
        $this->assertSame('pending', $result->status);
        $this->assertNotNull($result->reference);

        $fresh = $app->fresh();
        $this->assertSame('pending', $fresh->payout_status);
        $this->assertSame('mtn', $fresh->payout_provider);
        $this->assertSame($result->reference, $fresh->payout_reference);
        $this->assertNotNull($fresh->payout_initiated_at);

        Http::assertSent(fn ($request) =>
            str_contains($request->url(), '/disbursement/v1_0/transfer')
            && $request->hasHeader('X-Reference-Id', $result->reference)
            && $request->hasHeader('X-Target-Environment', 'sandbox')
            && $request['payee']['partyId'] === '237677123456'
        );
    }

    public function test_disburse_without_number_fails(): void
    {
        $app = $this->paidApplication();
        $app->practitioner->practitionerProfile->update(['payout_number' => null]);
        $app->refresh();

        $result = (new MtnMomoPayoutGateway())->disburse($app, 50000, 'XAF');

        $this->assertFalse($result->success);
    }

    public function test_status_maps_successful_to_paid(): void
    {
        Http::fake([
            '*/disbursement/token/'          => Http::response(['access_token' => 'tok-123', 'expires_in' => 3600], 200),
            '*/disbursement/v1_0/transfer/*' => Http::response(['status' => 'SUCCESSFUL'], 200),
        ]);

        $app = $this->paidApplication();
        $app->update(['payout_reference' => 'ref-abc', 'payout_provider' => 'mtn']);

        $this->assertSame('paid', (new MtnMomoPayoutGateway())->status($app));
    }

    public function test_status_maps_failed(): void
    {
        Http::fake([
            '*/disbursement/token/'          => Http::response(['access_token' => 'tok-123', 'expires_in' => 3600], 200),
            '*/disbursement/v1_0/transfer/*' => Http::response(['status' => 'FAILED', 'reason' => 'PAYEE_NOT_FOUND'], 200),
        ]);

        $app = $this->paidApplication();
        $app->update(['payout_reference' => 'ref-abc', 'payout_provider' => 'mtn']);

        $this->assertSame('failed', (new MtnMomoPayoutGateway())->status($app));
    }
}
