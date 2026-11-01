<?php

namespace Tests\Feature;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\PartnerInstitution;
use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPublicCatalogueWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_institution_and_practitioner_program_writes_share_guarded_catalogue_commands(): void
    {
        Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $institution = AdminWorkflowAdapter::record('admin.publishing.partnerInstitutions.create', [
            'name' => 'Fixture Institute', 'type' => 'research_institute', 'country' => 'CM', 'is_active' => true,
            'is_featured' => false, 'sort_order' => 1,
        ]);
        AdminWorkflowAdapter::run('admin.publishing.partnerInstitutions.update', ['is_featured' => true, 'sort_order' => 0], ['institution' => $institution]);
        $institutionQuery = app(WorkflowDispatcher::class)->execute('admin.publishing.partnerInstitutions.index', new WorkflowInput(actor: $admin));
        $this->assertSame($institution->id, $institutionQuery->data['items'][0]->id);

        $program = AdminWorkflowAdapter::record('admin.practitioners.programs.create', [
            'title' => 'Fixture programme', 'type' => 'volunteer', 'program_type' => 'general', 'status' => 'draft',
            'starts_at' => '2026-11-01', 'ends_at' => '2026-12-01',
        ]);
        AdminWorkflowAdapter::run('admin.practitioners.programs.update', ['status' => 'open'], ['program' => $program]);
        $applicant = User::factory()->create(['is_active' => true]);
        PractitionerApplication::create(['practitioner_id' => $applicant->id, 'program_id' => $program->id, 'motivation' => 'Fixture', 'status' => 'pending']);

        try {
            AdminWorkflowAdapter::run('admin.practitioners.programs.update', ['ends_at' => '2026-10-01'], ['program' => $program]);
            $this->fail('A programme cannot end before it starts.');
        } catch (ValidationException) {
            $this->assertDatabaseHas('practitioner_programs', ['id' => $program->id, 'ends_at' => '2026-12-01 00:00:00']);
        }

        $this->expectException(ValidationException::class);
        AdminWorkflowAdapter::run('admin.practitioners.programs.update', ['type' => 'paid'], ['program' => $program]);
    }
}
