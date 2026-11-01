<?php

namespace Tests\Feature;

use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Models\PartnerApplication;
use App\Models\TesterApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminExternalApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_applications_are_administered_through_limited_commands_and_history_is_kept(): void
    {
        Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $partner = PartnerApplication::create(['organization_name' => 'Fixture Clinic', 'contact_name' => 'Fixture Contact', 'email' => 'partner@example.test', 'country' => 'CM', 'partner_type' => 'hospital', 'description' => 'Fictional fixture', 'status' => 'pending']);
        $tester = TesterApplication::create(['name' => 'Fixture Tester', 'email' => 'tester@example.test', 'profession' => 'doctor', 'country' => 'CM', 'years_experience' => 2, 'motivation' => 'Fictional fixture', 'status' => 'pending']);

        AdminWorkflowAdapter::run('admin.enquiries.partnerApplications.update', ['status' => 'reviewing', 'admin_notes' => 'Reviewed fixture'], ['partnerApplication' => $partner]);
        AdminWorkflowAdapter::run('admin.enquiries.testerApplications.update', ['status' => 'accepted', 'admin_notes' => 'Accepted fixture'], ['testerApplication' => $tester]);
        $this->assertDatabaseHas('partner_applications', ['id' => $partner->id, 'status' => 'reviewing', 'admin_notes' => 'Reviewed fixture']);
        $this->assertDatabaseHas('tester_applications', ['id' => $tester->id, 'status' => 'accepted', 'admin_notes' => 'Accepted fixture']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'enquiries.partner_application_admin_updated.v1', 'actor_id' => $admin->id]);

        try {
            AdminWorkflowAdapter::run('admin.enquiries.partnerApplications.update', ['email' => 'changed@example.test'], ['partnerApplication' => $partner]);
            $this->fail('Partner submission contact information must remain immutable through admin edits.');
        } catch (ValidationException) {
            $this->assertDatabaseHas('partner_applications', ['id' => $partner->id, 'email' => 'partner@example.test']);
        }

        $this->expectException(ValidationException::class);
        AdminWorkflowAdapter::run('admin.enquiries.testerApplications.update', ['status' => 'rejected'], ['testerApplication' => $tester]);
    }
}
