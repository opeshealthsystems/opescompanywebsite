<?php

namespace Tests\Feature;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Models\PractitionerProfile;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPractitionerRecordWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_reads_and_edits_share_commands_without_mutating_lifecycle_fields(): void
    {
        Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $practitioner = User::factory()->create(['is_active' => true]);
        $program = PractitionerProgram::create(['title' => 'Fictional programme', 'type' => 'volunteer', 'program_type' => 'general', 'status' => 'open']);
        $application = PractitionerApplication::create(['practitioner_id' => $practitioner->id, 'program_id' => $program->id, 'motivation' => 'Fictional fixture', 'status' => 'pending']);
        $profile = PractitionerProfile::create(['user_id' => $practitioner->id, 'profession' => 'doctor', 'registration_number' => 'PRIVATE-REG', 'payout_number' => '+237600000001', 'is_verified' => false]);
        $finding = PractitionerFinding::create(['application_id' => $application->id, 'practitioner_id' => $practitioner->id, 'overall_rating' => 3, 'is_published' => false]);
        $this->actingAs($admin);

        $profileResult = app(WorkflowDispatcher::class)->execute('admin.practitioners.profiles.show', new WorkflowInput(actor: $admin, parameters: ['profile' => $profile]));
        $this->assertArrayNotHasKey('registration_number', $profileResult->data['profile']);
        $this->assertArrayNotHasKey('payout_number', $profileResult->data['profile']);
        AdminWorkflowAdapter::run('admin.practitioners.profiles.update', ['specialty' => 'Family medicine'], ['profile' => $profile]);
        AdminWorkflowAdapter::run('admin.practitioners.findings.update', ['overall_rating' => 5], ['finding' => $finding]);
        AdminWorkflowAdapter::run('admin.practitioners.applications.updateNotes', ['admin_notes' => 'Reviewed fixture'], ['application' => $application]);

        $this->assertDatabaseHas('practitioner_profiles', ['id' => $profile->id, 'specialty' => 'Family medicine', 'is_verified' => false]);
        $this->assertDatabaseHas('practitioner_findings', ['id' => $finding->id, 'overall_rating' => 5, 'is_published' => false]);
        $this->assertDatabaseHas('practitioner_applications', ['id' => $application->id, 'admin_notes' => 'Reviewed fixture', 'status' => 'pending']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'practitioners.profile_updated.v1', 'actor_id' => $admin->id]);

        $this->expectException(ValidationException::class);
        AdminWorkflowAdapter::run('admin.practitioners.applications.updateNotes', ['status' => 'approved'], ['application' => $application]);
    }
}
