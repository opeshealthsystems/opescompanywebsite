<?php

namespace Tests\Feature;

use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

// Helper to create a JobOpening without a factory
function makeOpening(array $attrs = []): JobOpening
{
    $creator = User::factory()->create();

    return JobOpening::create(array_merge([
        'title'          => 'Test Engineer',
        'type'           => 'full_time',
        'status'         => 'open',
        'openings_count' => 1,
        'created_by'     => $creator->id,
    ], $attrs));
}

function makeApplication(int $openingId, array $attrs = []): JobApplication
{
    return JobApplication::create(array_merge([
        'job_opening_id' => $openingId,
        'applicant_name' => 'Test Applicant',
        'email'          => 'applicant' . uniqid() . '@example.com',
        'status'         => 'received',
        'applied_at'     => now(),
    ], $attrs));
}

class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_job_applications_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('job_applications'));
    }

    public function test_job_application_belongs_to_job_opening(): void
    {
        $opening = makeOpening();
        $application = makeApplication($opening->id);

        $this->assertInstanceOf(JobOpening::class, $application->jobOpening);
        $this->assertEquals($opening->id, $application->jobOpening->id);
    }

    public function test_job_application_status_options_covers_full_workflow(): void
    {
        $options = JobApplication::statusOptions();

        $this->assertArrayHasKey('received',    $options);
        $this->assertArrayHasKey('reviewing',   $options);
        $this->assertArrayHasKey('shortlisted', $options);
        $this->assertArrayHasKey('interviewed', $options);
        $this->assertArrayHasKey('offered',     $options);
        $this->assertArrayHasKey('hired',       $options);
        $this->assertArrayHasKey('rejected',    $options);
    }

    public function test_job_application_default_status_is_received(): void
    {
        $opening = makeOpening();
        $application = makeApplication($opening->id);

        $this->assertEquals('received', $application->status);
    }

    public function test_job_application_workflow_transitions(): void
    {
        $opening = makeOpening();
        $application = makeApplication($opening->id);

        $application->update(['status' => 'shortlisted']);
        $this->assertEquals('shortlisted', $application->fresh()->status);

        $application->update(['status' => 'interviewed']);
        $this->assertEquals('interviewed', $application->fresh()->status);

        $application->update(['status' => 'offered']);
        $this->assertEquals('offered', $application->fresh()->status);

        $application->update(['status' => 'hired']);
        $this->assertEquals('hired', $application->fresh()->status);
    }

    public function test_admin_can_access_job_applications_admin_panel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/job-applications')
            ->assertOk();
    }

    public function test_customer_cannot_access_job_applications_admin_panel(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)
            ->get('/admin/job-applications')
            ->assertForbidden();
    }

    public function test_multiple_applications_per_opening(): void
    {
        $opening = makeOpening();
        makeApplication($opening->id);
        makeApplication($opening->id);
        makeApplication($opening->id);

        $this->assertEquals(3, $opening->applications()->count());
    }
}
