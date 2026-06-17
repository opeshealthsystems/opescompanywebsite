<?php

namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use App\Services\Payouts\OrangeMoneyPayoutGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrangeMoneyPayoutGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_orange_driver_throws_until_implemented(): void
    {
        $user = User::factory()->create();
        $program = PractitionerProgram::factory()->paid()->create();
        $app = PractitionerApplication::factory()->create([
            'practitioner_id' => $user->id,
            'program_id'      => $program->id,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Orange Money payout driver is not yet implemented');

        (new OrangeMoneyPayoutGateway())->disburse($app, 50000, 'XAF');
    }
}
