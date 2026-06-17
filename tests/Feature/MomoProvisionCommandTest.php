<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MomoProvisionCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('payouts.mtn_momo', [
            'base_url'         => 'https://sandbox.momodeveloper.mtn.com',
            'subscription_key' => 'test-sub-key',
            'api_user'         => '',
            'api_key'          => '',
            'environment'      => 'sandbox',
        ]);
    }

    public function test_provision_creates_api_user_and_key(): void
    {
        Http::fake([
            '*/v1_0/apiuser'         => Http::response('', 201),
            '*/v1_0/apiuser/*/apikey' => Http::response(['apiKey' => 'provisioned-key-123'], 201),
        ]);

        $this->artisan('momo:provision')
            ->expectsOutputToContain('MTN_MOMO_API_KEY=provisioned-key-123')
            ->assertExitCode(0);
    }

    public function test_provision_fails_without_subscription_key(): void
    {
        config()->set('payouts.mtn_momo.subscription_key', '');

        $this->artisan('momo:provision')->assertExitCode(1);
    }

    public function test_provision_reports_api_user_creation_failure(): void
    {
        Http::fake([
            '*/v1_0/apiuser' => Http::response(['message' => 'bad key'], 401),
        ]);

        $this->artisan('momo:provision')->assertExitCode(1);
    }
}
