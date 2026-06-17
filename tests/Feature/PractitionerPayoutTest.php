<?php

namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PractitionerPayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function practitioner(): User
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM']);

        return $user;
    }

    public function test_approving_a_paid_program_application_marks_payout_pending(): void
    {
        $program = PractitionerProgram::factory()->paid()->create();
        $application = PractitionerApplication::factory()->create([
            'practitioner_id' => $this->practitioner()->id,
            'program_id'      => $program->id,
            'status'          => 'pending',
        ]);

        $application->markApproved(1);

        $this->assertSame('approved', $application->fresh()->status);
        $this->assertSame('pending', $application->fresh()->payout_status);
    }

    public function test_approving_a_volunteer_program_application_leaves_payout_not_applicable(): void
    {
        $program = PractitionerProgram::factory()->create(['type' => 'volunteer']);
        $application = PractitionerApplication::factory()->create([
            'practitioner_id' => $this->practitioner()->id,
            'program_id'      => $program->id,
            'status'          => 'pending',
        ]);

        $application->markApproved(1);

        $this->assertSame('not_applicable', $application->fresh()->payout_status);
    }

    public function test_recording_a_payout_persists_amount_currency_reference_and_timestamp(): void
    {
        $program = PractitionerProgram::factory()->paid()->create();
        $application = PractitionerApplication::factory()->create([
            'practitioner_id' => $this->practitioner()->id,
            'program_id'      => $program->id,
            'status'          => 'approved',
            'payout_status'   => 'pending',
        ]);

        $application->update([
            'payout_status'    => 'paid',
            'payout_amount'    => 50000,
            'payout_currency'  => 'XAF',
            'payout_reference' => 'MOMO-12345',
            'paid_at'          => now(),
        ]);

        $fresh = $application->fresh();
        $this->assertSame('paid', $fresh->payout_status);
        $this->assertSame('50000.00', (string) $fresh->payout_amount);
        $this->assertSame('XAF', $fresh->payout_currency);
        $this->assertSame('MOMO-12345', $fresh->payout_reference);
        $this->assertNotNull($fresh->paid_at);
    }

    public function test_practitioner_application_show_displays_compensation_for_paid_program(): void
    {
        $practitioner = $this->practitioner();
        $program = PractitionerProgram::factory()->paid()->create(['compensation' => '50,000 XAF on completion']);
        $application = PractitionerApplication::factory()->create([
            'practitioner_id' => $practitioner->id,
            'program_id'      => $program->id,
            'status'          => 'approved',
            'payout_status'   => 'pending',
        ]);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/applications/' . $application->id)
            ->assertOk()
            ->assertSee('Compensation')
            ->assertSee('50,000 XAF on completion');
    }

    public function test_volunteer_application_show_hides_compensation_section(): void
    {
        $practitioner = $this->practitioner();
        $program = PractitionerProgram::factory()->create(['type' => 'volunteer']);
        $application = PractitionerApplication::factory()->create([
            'practitioner_id' => $practitioner->id,
            'program_id'      => $program->id,
            'status'          => 'approved',
        ]);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/applications/' . $application->id)
            ->assertOk()
            ->assertDontSee('Compensation');
    }
}
