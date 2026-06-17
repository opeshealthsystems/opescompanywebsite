<?php

namespace Tests\Feature;

use App\Mail\PayoutSettled;
use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PollPayoutsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        config()->set('payouts.mtn_momo', [
            'base_url' => 'https://sandbox.momodeveloper.mtn.com',
            'subscription_key' => 'k', 'api_user' => 'u', 'api_key' => 'a', 'environment' => 'sandbox',
        ]);
        Mail::fake();
    }

    private function pendingMtnPayout(): PractitionerApplication
    {
        $user = User::factory()->create();
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM', 'payout_number' => '677123456']);
        $program = PractitionerProgram::factory()->paid()->create();

        return PractitionerApplication::factory()->create([
            'practitioner_id'     => $user->id,
            'program_id'          => $program->id,
            'status'              => 'approved',
            'payout_status'       => 'pending',
            'payout_provider'     => 'mtn',
            'payout_reference'    => 'ref-1',
            'payout_amount'       => 50000,
            'payout_currency'     => 'XAF',
            'payout_initiated_at' => now(),
        ]);
    }

    public function test_poll_marks_successful_payout_paid_and_mails_practitioner(): void
    {
        Http::fake([
            '*/disbursement/token/'          => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            '*/disbursement/v1_0/transfer/*' => Http::response(['status' => 'SUCCESSFUL'], 200),
        ]);
        $app = $this->pendingMtnPayout();

        $this->artisan('payouts:poll')->assertExitCode(0);

        $fresh = $app->fresh();
        $this->assertSame('paid', $fresh->payout_status);
        $this->assertNotNull($fresh->paid_at);
        Mail::assertQueued(PayoutSettled::class);
    }

    public function test_poll_marks_failed_payout(): void
    {
        Http::fake([
            '*/disbursement/token/'          => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            '*/disbursement/v1_0/transfer/*' => Http::response(['status' => 'FAILED', 'reason' => 'PAYEE_NOT_FOUND'], 200),
        ]);
        $app = $this->pendingMtnPayout();

        $this->artisan('payouts:poll')->assertExitCode(0);

        $fresh = $app->fresh();
        $this->assertSame('failed', $fresh->payout_status);
        $this->assertNotNull($fresh->payout_failure_reason);
    }
}
