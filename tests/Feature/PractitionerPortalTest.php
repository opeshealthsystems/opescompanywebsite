<?php

namespace Tests\Feature;

use App\Mail\PractitionerApplicationReceived;
use App\Mail\PractitionerWelcome;
use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PractitionerPortalTest extends TestCase
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

    // ── Registration ──────────────────────────────────────────────────────

    public function test_practitioner_can_register(): void
    {
        Mail::fake();

        $response = $this->post('/practitioners/register', [
            'name'                  => 'Dr. Ngu Patrick',
            'email'                 => 'ngu@clinic.cm',
            'password'              => 'Secret1234!',
            'password_confirmation' => 'Secret1234!',
            'phone'                 => '+237612000000',
            'profession'            => 'doctor',
            'specialty'             => 'Cardiology',
            'workplace_name'        => 'Douala General Hospital',
            'workplace_country'     => 'CM',
            'locale'                => 'en',
        ]);

        $response->assertRedirect('/en/practitioner/dashboard');

        $this->assertDatabaseHas('users', ['email' => 'ngu@clinic.cm']);

        $user = User::where('email', 'ngu@clinic.cm')->first();
        $this->assertTrue($user->hasRole('practitioner'));
        $this->assertDatabaseHas('practitioner_profiles', [
            'user_id'    => $user->id,
            'profession' => 'doctor',
        ]);

        Mail::assertQueued(PractitionerWelcome::class, function ($mail) use ($user) {
            return $mail->user->id === $user->id;
        });
    }

    public function test_registration_validation_rejects_missing_required_fields(): void
    {
        $response = $this->post('/practitioners/register', [
            'name'       => '',
            'email'      => '',
            'password'   => '',
            'profession' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'profession']);
        $this->assertDatabaseCount('users', 0);
    }

    // ── Dashboard & profile ───────────────────────────────────────────────

    public function test_practitioner_can_load_dashboard_and_profile(): void
    {
        $practitioner = $this->practitioner();

        $this->actingAs($practitioner)->get('/en/practitioner/dashboard')->assertOk();
        $this->actingAs($practitioner)->get('/en/practitioner/profile')->assertOk();
    }

    public function test_practitioner_can_update_profile(): void
    {
        $practitioner = $this->practitioner();

        $response = $this->actingAs($practitioner)->put('/en/practitioner/profile', [
            'name'                => 'Updated Name',
            'phone'               => '+237699111222',
            'profession'          => 'nurse',
            'specialty'           => 'Pediatrics',
            'workplace_name'      => 'New Clinic',
            'workplace_city'      => 'Yaounde',
            'workplace_country'   => 'CM',
            'years_of_experience' => 7,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id'    => $practitioner->id,
            'name'  => 'Updated Name',
            'phone' => '+237699111222',
        ]);
        $this->assertDatabaseHas('practitioner_profiles', [
            'user_id'        => $practitioner->id,
            'profession'     => 'nurse',
            'workplace_name' => 'New Clinic',
            'workplace_city' => 'Yaounde',
        ]);
    }

    // ── Programs ──────────────────────────────────────────────────────────

    public function test_practitioner_sees_only_open_programs(): void
    {
        $practitioner = $this->practitioner();

        $open   = PractitionerProgram::factory()->create(['title' => 'Open Volunteer Programme']);
        $draft  = PractitionerProgram::factory()->draft()->create(['title' => 'Draft Programme']);
        $closed = PractitionerProgram::factory()->closed()->create(['title' => 'Closed Programme']);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/programs')
            ->assertOk()
            ->assertSee('Open Volunteer Programme')
            ->assertDontSee('Draft Programme')
            ->assertDontSee('Closed Programme');
    }

    public function test_practitioner_can_apply_to_a_program(): void
    {
        Mail::fake();

        $practitioner = $this->practitioner();
        $program      = PractitionerProgram::factory()->create();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/programs/' . $program->id . '/apply', [
                'motivation' => 'I want to help improve healthcare technology.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('practitioner_applications', [
            'practitioner_id' => $practitioner->id,
            'program_id'      => $program->id,
            'status'          => 'pending',
        ]);

        Mail::assertQueued(PractitionerApplicationReceived::class);
    }

    public function test_practitioner_cannot_apply_twice_to_same_program(): void
    {
        Mail::fake();

        $practitioner = $this->practitioner();
        $program      = PractitionerProgram::factory()->create();

        PractitionerApplication::factory()->create([
            'practitioner_id' => $practitioner->id,
            'program_id'      => $program->id,
        ]);

        $this->actingAs($practitioner)
            ->post('/en/practitioner/programs/' . $program->id . '/apply', [
                'motivation' => 'Trying again.',
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('practitioner_applications', 1);
    }

    // ── Findings ──────────────────────────────────────────────────────────

    public function test_approved_application_can_load_findings_create_page(): void
    {
        $practitioner = $this->practitioner();

        $application = PractitionerApplication::factory()->approved()->create([
            'practitioner_id' => $practitioner->id,
        ]);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/applications/' . $application->id . '/findings/create')
            ->assertOk();
    }

    public function test_pending_application_is_blocked_from_findings_create_page(): void
    {
        $practitioner = $this->practitioner();

        $application = PractitionerApplication::factory()->create([
            'practitioner_id' => $practitioner->id,
            'status'          => 'pending',
        ]);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/applications/' . $application->id . '/findings/create')
            ->assertForbidden();
    }

    public function test_practitioner_can_store_findings_with_valid_ratings(): void
    {
        $practitioner = $this->practitioner();

        $application = PractitionerApplication::factory()->approved()->create([
            'practitioner_id' => $practitioner->id,
        ]);

        $this->actingAs($practitioner)
            ->post('/en/practitioner/applications/' . $application->id . '/findings', [
                'overall_rating'        => 5,
                'wait_time_rating'      => 4,
                'data_integrity_rating' => 5,
                'usability_rating'      => 3,
                'findings_text'         => 'Excellent system overall.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('practitioner_findings', [
            'application_id'  => $application->id,
            'practitioner_id' => $practitioner->id,
            'overall_rating'  => 5,
            'usability_rating' => 3,
        ]);
    }

    public function test_findings_store_validates_rating_range(): void
    {
        $practitioner = $this->practitioner();

        $application = PractitionerApplication::factory()->approved()->create([
            'practitioner_id' => $practitioner->id,
        ]);

        $this->actingAs($practitioner)
            ->post('/en/practitioner/applications/' . $application->id . '/findings', [
                'overall_rating'        => 9,
                'wait_time_rating'      => 0,
                'data_integrity_rating' => 6,
                'usability_rating'      => 7,
            ])
            ->assertSessionHasErrors([
                'overall_rating',
                'wait_time_rating',
                'data_integrity_rating',
                'usability_rating',
            ]);

        $this->assertDatabaseCount('practitioner_findings', 0);
    }

    // ── Ownership ─────────────────────────────────────────────────────────

    public function test_practitioner_cannot_view_another_practitioners_application(): void
    {
        $owner = $this->practitioner();
        $other = $this->practitioner();

        $application = PractitionerApplication::factory()->create([
            'practitioner_id' => $owner->id,
        ]);

        $this->actingAs($other)
            ->get('/en/practitioner/applications/' . $application->id)
            ->assertForbidden();
    }
}
