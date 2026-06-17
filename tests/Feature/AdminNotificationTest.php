<?php

namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        Mail::fake();
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        return $user;
    }

    private function practitioner(): User
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM']);
        return $user;
    }

    private function verifiedPractitioner(): User
    {
        $user = $this->practitioner();
        $user->practitionerProfile->update(['is_verified' => true]);
        $user->refresh();
        return $user;
    }

    private function customer(): User
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        return $user;
    }

    public function test_suggestion_submission_notifies_admin(): void
    {
        $admin = $this->admin();
        $practitioner = $this->practitioner();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/suggestions', [
                'title'    => 'Add dark mode',
                'category' => 'feature_request',
                'body'     => 'It would be great to have a dark mode option available.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('notifications', ['notifiable_id' => $admin->id]);
        $this->assertGreaterThan(0, $admin->fresh()->notifications()->count());
    }

    public function test_bug_report_submission_notifies_admin(): void
    {
        $admin = $this->admin();
        $practitioner = $this->practitioner();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/bug-reports', [
                'title'       => 'Login button broken',
                'severity'    => 'high',
                'description' => 'The login button does not respond when clicked.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('notifications', ['notifiable_id' => $admin->id]);
    }

    public function test_service_request_submission_notifies_admin(): void
    {
        $admin = $this->admin();
        $customer = $this->customer();

        $this->actingAs($customer)
            ->post('/en/customer/service-requests', [
                'type'           => 'installation',
                'description'    => 'Need installation of new equipment.',
                'preferred_date' => now()->addWeek()->toDateString(),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('notifications', ['notifiable_id' => $admin->id]);
    }

    public function test_program_application_notifies_admin(): void
    {
        $admin = $this->admin();
        $practitioner = $this->practitioner();
        $program = PractitionerProgram::factory()->create();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/programs/' . $program->id . '/apply', [
                'motivation' => 'I would love to contribute to this programme.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('notifications', ['notifiable_id' => $admin->id]);
    }

    public function test_findings_submission_notifies_admin(): void
    {
        $admin = $this->admin();
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

        $this->assertDatabaseHas('notifications', ['notifiable_id' => $admin->id]);
    }
}
