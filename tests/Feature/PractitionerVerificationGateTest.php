<?php

namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PractitionerVerificationGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function verifiedPractitioner(): User
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $user->practitionerProfile()->create([
            'profession'        => 'doctor',
            'workplace_country' => 'CM',
            'is_verified'       => true,
        ]);
        return $user;
    }

    private function unverifiedPractitioner(): User
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $user->practitionerProfile()->create([
            'profession'        => 'doctor',
            'workplace_country' => 'CM',
            'is_verified'       => false,
        ]);
        return $user;
    }

    // ── Paid programmes ───────────────────────────────────────────────────

    public function test_unverified_practitioner_cannot_apply_to_paid_program(): void
    {
        Mail::fake();

        $practitioner = $this->unverifiedPractitioner();
        $program      = PractitionerProgram::factory()->paid()->create();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/programs/' . $program->id . '/apply', [
                'motivation' => 'Please let me in.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('practitioner_applications', [
            'practitioner_id' => $practitioner->id,
            'program_id'      => $program->id,
        ]);
    }

    public function test_verified_practitioner_can_apply_to_paid_program(): void
    {
        Mail::fake();

        $practitioner = $this->verifiedPractitioner();
        $program      = PractitionerProgram::factory()->paid()->create();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/programs/' . $program->id . '/apply', [
                'motivation' => 'I am credentialed and ready.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('practitioner_applications', [
            'practitioner_id' => $practitioner->id,
            'program_id'      => $program->id,
            'status'          => 'pending',
        ]);
    }

    // ── Volunteer programmes ──────────────────────────────────────────────

    public function test_unverified_practitioner_can_apply_to_volunteer_program(): void
    {
        Mail::fake();

        $practitioner = $this->unverifiedPractitioner();
        $program      = PractitionerProgram::factory()->create(); // volunteer by default

        $this->actingAs($practitioner)
            ->post('/en/practitioner/programs/' . $program->id . '/apply', [
                'motivation' => 'Happy to volunteer.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('practitioner_applications', [
            'practitioner_id' => $practitioner->id,
            'program_id'      => $program->id,
        ]);
    }

    public function test_verified_practitioner_can_apply_to_volunteer_program(): void
    {
        Mail::fake();

        $practitioner = $this->verifiedPractitioner();
        $program      = PractitionerProgram::factory()->create();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/programs/' . $program->id . '/apply', [
                'motivation' => 'Happy to volunteer.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('practitioner_applications', [
            'practitioner_id' => $practitioner->id,
            'program_id'      => $program->id,
        ]);
    }

    // ── Findings ──────────────────────────────────────────────────────────

    public function test_unverified_practitioner_cannot_load_findings_create_page(): void
    {
        $practitioner = $this->unverifiedPractitioner();

        $application = PractitionerApplication::factory()->approved()->create([
            'practitioner_id' => $practitioner->id,
        ]);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/applications/' . $application->id . '/findings/create')
            ->assertForbidden();
    }

    public function test_unverified_practitioner_cannot_store_findings(): void
    {
        $practitioner = $this->unverifiedPractitioner();

        $application = PractitionerApplication::factory()->approved()->create([
            'practitioner_id' => $practitioner->id,
        ]);

        $this->actingAs($practitioner)
            ->post('/en/practitioner/applications/' . $application->id . '/findings', [
                'overall_rating' => 5,
                'findings_text'  => 'Great system.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('practitioner_findings', 0);
    }

    public function test_verified_practitioner_with_approved_application_can_submit_findings(): void
    {
        $practitioner = $this->verifiedPractitioner();

        $application = PractitionerApplication::factory()->approved()->create([
            'practitioner_id' => $practitioner->id,
        ]);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/applications/' . $application->id . '/findings/create')
            ->assertOk();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/applications/' . $application->id . '/findings', [
                'overall_rating'        => 5,
                'wait_time_rating'      => 4,
                'data_integrity_rating' => 5,
                'usability_rating'      => 4,
                'findings_text'         => 'Excellent system overall.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('practitioner_findings', [
            'application_id'  => $application->id,
            'practitioner_id' => $practitioner->id,
            'overall_rating'  => 5,
        ]);
    }
}
