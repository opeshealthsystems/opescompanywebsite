<?php

namespace Tests\Feature;

use App\Enums\PractitionerTier;
use App\Mail\PractitionerApplicationReceived;
use App\Mail\PractitionerWelcome;
use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
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

    /** A practitioner whose profile has been verified by an admin. */
    private function verifiedPractitioner(): User
    {
        $user = $this->practitioner();
        $user->practitionerProfile->update(['is_verified' => true]);
        $user->refresh();
        return $user;
    }

    // ── Registration ──────────────────────────────────────────────────────

    public function test_practitioner_can_register(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'account_type'          => 'practitioner',
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
        $response = $this->post('/register', [
            'account_type' => 'practitioner',
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
        $practitioner = $this->verifiedPractitioner();

        $application = PractitionerApplication::factory()->approved()->create([
            'practitioner_id' => $practitioner->id,
        ]);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/applications/' . $application->id . '/findings/create')
            ->assertOk();
    }

    public function test_pending_application_is_blocked_from_findings_create_page(): void
    {
        $practitioner = $this->verifiedPractitioner();

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
        $practitioner = $this->verifiedPractitioner();

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
        $practitioner = $this->verifiedPractitioner();

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

    // ── Tier ladder ───────────────────────────────────────────────────────

    public function test_unverified_practitioner_is_associate_even_with_published_findings(): void
    {
        $practitioner = $this->practitioner(); // profile is_verified defaults to false

        PractitionerFinding::factory()->published()->count(10)->create([
            'practitioner_id' => $practitioner->id,
        ]);

        $this->assertSame(PractitionerTier::Associate, $practitioner->fresh()->practitionerTier());
    }

    public function test_verified_practitioner_tier_climbs_with_published_findings(): void
    {
        $practitioner = $this->practitioner();
        $practitioner->practitionerProfile->update(['is_verified' => true]);

        $this->assertSame(PractitionerTier::Verified, $practitioner->fresh()->practitionerTier());

        PractitionerFinding::factory()->published()->count(3)->create([
            'practitioner_id' => $practitioner->id,
        ]);
        $this->assertSame(PractitionerTier::Distinguished, $practitioner->fresh()->practitionerTier());

        PractitionerFinding::factory()->published()->count(5)->create([
            'practitioner_id' => $practitioner->id,
        ]);
        $this->assertSame(PractitionerTier::Fellow, $practitioner->fresh()->practitionerTier());
    }

    public function test_unpublished_findings_do_not_count_toward_tier(): void
    {
        $practitioner = $this->practitioner();
        $practitioner->practitionerProfile->update(['is_verified' => true]);

        PractitionerFinding::factory()->count(8)->create([
            'practitioner_id' => $practitioner->id,
            'is_published'    => false,
        ]);

        $this->assertSame(PractitionerTier::Verified, $practitioner->fresh()->practitionerTier());
    }

    // ── Paid-program gate ─────────────────────────────────────────────────

    public function test_associate_cannot_apply_to_paid_program(): void
    {
        $practitioner = $this->practitioner(); // unverified → Associate
        $program      = PractitionerProgram::factory()->paid()->create();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/programs/' . $program->id . '/apply', [
                'motivation' => 'I would like to join.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('practitioner_applications', 0);
    }

    public function test_verified_practitioner_can_apply_to_paid_program(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $practitioner = $this->practitioner();
        $practitioner->practitionerProfile->update(['is_verified' => true]);
        $program = PractitionerProgram::factory()->paid()->create();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/programs/' . $program->id . '/apply', [
                'motivation' => 'Verified and ready.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('practitioner_applications', [
            'practitioner_id' => $practitioner->id,
            'program_id'      => $program->id,
        ]);
    }

    public function test_associate_can_still_apply_to_volunteer_program(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $practitioner = $this->practitioner(); // unverified
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

    // ── Paid-program view gate ────────────────────────────────────────────

    public function test_paid_program_show_blocks_apply_form_for_associate(): void
    {
        $practitioner = $this->practitioner(); // unverified
        $program      = PractitionerProgram::factory()->paid()->create();

        $this->actingAs($practitioner)
            ->get('/en/practitioner/programs/' . $program->id)
            ->assertOk()
            ->assertSee('Verification Required')
            ->assertDontSee('Submit Application');
    }

    public function test_paid_program_show_allows_apply_form_for_verified(): void
    {
        $practitioner = $this->practitioner();
        $practitioner->practitionerProfile->update(['is_verified' => true]);
        $program = PractitionerProgram::factory()->paid()->create();

        $this->actingAs($practitioner)
            ->get('/en/practitioner/programs/' . $program->id)
            ->assertOk()
            ->assertSee('Submit Application');
    }

    public function test_paid_program_index_shows_verified_only_for_associate(): void
    {
        $practitioner = $this->practitioner(); // unverified
        PractitionerProgram::factory()->paid()->create(['title' => 'Paid Pilot']);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/programs')
            ->assertOk()
            ->assertSee('Verified only');
    }

    // ── Dashboard tier badge ──────────────────────────────────────────────

    public function test_dashboard_displays_current_tier_badge(): void
    {
        $practitioner = $this->practitioner();
        $practitioner->practitionerProfile->update(['is_verified' => true]);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/dashboard')
            ->assertOk()
            ->assertSee('Verified');
    }

    // ── Tier priority ordering ────────────────────────────────────────────

    public function test_applications_sort_by_tier_priority(): void
    {
        $program = PractitionerProgram::factory()->paid()->create();

        // Fellow: verified + 8 published findings
        $fellow = $this->practitioner();
        $fellow->practitionerProfile->update(['is_verified' => true]);
        PractitionerFinding::factory()->published()->count(8)->create(['practitioner_id' => $fellow->id]);

        // Verified: verified + 0 findings
        $verified = $this->practitioner();
        $verified->practitionerProfile->update(['is_verified' => true]);

        // Distinguished: verified + 3 published findings
        $distinguished = $this->practitioner();
        $distinguished->practitionerProfile->update(['is_verified' => true]);
        PractitionerFinding::factory()->published()->count(3)->create(['practitioner_id' => $distinguished->id]);

        // Associate: unverified — must sort last (exercises the leftJoin null path)
        $associate = $this->practitioner();

        foreach ([$verified, $associate, $fellow, $distinguished] as $u) {
            PractitionerApplication::factory()->create([
                'practitioner_id' => $u->id,
                'program_id'      => $program->id,
            ]);
        }

        $ordered = PractitionerApplication::where('program_id', $program->id)
            ->byTierPriority()
            ->pluck('practitioner_id')
            ->all();

        $this->assertSame([$fellow->id, $distinguished->id, $verified->id, $associate->id], $ordered);
    }

    // ── Admin Filament tier display ───────────────────────────────────────

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        return $user;
    }

    public function test_admin_can_load_practitioner_profiles_list_with_tier_column(): void
    {
        $admin = $this->admin();
        $practitioner = $this->practitioner();
        $practitioner->practitionerProfile->update(['is_verified' => true]);

        $this->actingAs($admin)
            ->get('/admin/practitioner-profiles')
            ->assertOk()
            ->assertSee('Tier');
    }

    public function test_admin_can_load_practitioner_applications_list_with_tier_column(): void
    {
        $admin = $this->admin();
        $practitioner = $this->practitioner();
        PractitionerApplication::factory()->create(['practitioner_id' => $practitioner->id]);

        $this->actingAs($admin)
            ->get('/admin/practitioner-applications')
            ->assertOk()
            ->assertSee('Tier');
    }
}
