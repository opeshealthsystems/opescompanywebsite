<?php

namespace Tests\Feature;

use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminServiceRequestWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_edits_and_cancellation_use_guarded_support_commands(): void
    {
        Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $customer = User::factory()->create(['is_active' => true]);
        $request = ServiceRequest::create(['customer_id' => $customer->id, 'type' => 'maintenance', 'description' => 'Fixture request', 'preferred_date' => '2026-11-10', 'status' => 'pending']);
        $this->actingAs($admin);

        AdminWorkflowAdapter::run('admin.support.serviceRequests.update', ['description' => 'Updated fixture', 'admin_notes' => 'Reviewed'], ['serviceRequest' => $request]);
        $this->assertDatabaseHas('service_requests', ['id' => $request->id, 'description' => 'Updated fixture', 'status' => 'pending']);

        try {
            AdminWorkflowAdapter::run('admin.support.serviceRequests.update', ['status' => 'completed'], ['serviceRequest' => $request]);
            $this->fail('Lifecycle status must use its dedicated action.');
        } catch (ValidationException) {
            $this->assertDatabaseHas('service_requests', ['id' => $request->id, 'status' => 'pending']);
        }

        AdminWorkflowAdapter::run('admin.support.serviceRequests.cancel', parameters: ['serviceRequest' => $request]);
        $this->assertDatabaseHas('service_requests', ['id' => $request->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'support.service_request_cancelled.v1', 'actor_id' => $admin->id]);

        $this->expectException(ValidationException::class);
        AdminWorkflowAdapter::run('admin.support.serviceRequests.cancel', parameters: ['serviceRequest' => $request]);
    }
}
