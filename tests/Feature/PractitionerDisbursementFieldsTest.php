<?php

namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PractitionerDisbursementFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_disbursement_fields_are_fillable_and_cast(): void
    {
        $program = PractitionerProgram::factory()->paid()->create();
        $app = PractitionerApplication::factory()->create([
            'practitioner_id'       => User::factory()->create()->id,
            'program_id'            => $program->id,
            'status'                => 'approved',
            'payout_status'         => 'pending',
            'payout_provider'       => 'mtn',
            'payout_initiated_at'   => now(),
            'payout_failure_reason' => null,
        ]);

        $fresh = $app->fresh();
        $this->assertSame('mtn', $fresh->payout_provider);
        $this->assertNotNull($fresh->payout_initiated_at);
    }

    public function test_is_payable_guard(): void
    {
        $program = PractitionerProgram::factory()->paid()->create();
        $app = PractitionerApplication::factory()->create([
            'practitioner_id' => User::factory()->create()->id,
            'program_id'      => $program->id,
            'status'          => 'approved',
            'payout_status'   => 'pending',
        ]);
        $this->assertTrue($app->isPayable());

        $app->update(['payout_status' => 'paid']);
        $this->assertFalse($app->fresh()->isPayable());
    }
}
