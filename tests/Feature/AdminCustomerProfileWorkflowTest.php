<?php

namespace Tests\Feature;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminCustomerProfileWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_profile_admin_reads_and_writes_share_customer_commands(): void
    {
        foreach (['admin', 'customer'] as $role) Role::findOrCreate($role, 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $customer = User::factory()->create(['is_active' => true]);
        $customer->assignRole('customer');
        $this->actingAs($admin);
        $data = ['user_id' => $customer->id, 'facility_name' => 'Fictional clinic profile', 'facility_type' => 'clinic', 'country' => null, 'city' => 'Yaounde', 'address' => 'Fictional street'];

        AdminWorkflowAdapter::run('admin.customerProfiles.create', $data);
        $profile = CustomerProfile::query()->where('user_id', $customer->id)->firstOrFail();
        $this->assertSame('CM', $profile->country);
        $this->assertDatabaseHas('business_outbox', ['type' => 'customer.profile_created.v1', 'actor_id' => $admin->id]);

        $result = app(WorkflowDispatcher::class)->execute('admin.customerProfiles.index', new WorkflowInput(['search' => 'Fictional clinic'], actor: $admin));
        $this->assertSame($profile->id, $result->data['items'][0]['id']);

        AdminWorkflowAdapter::run('admin.customerProfiles.update', [...$data, 'facility_name' => 'Updated fictional clinic', 'country' => 'CM'], ['customerProfile' => $profile->id]);
        $this->assertDatabaseHas('customer_profiles', ['id' => $profile->id, 'facility_name' => 'Updated fictional clinic']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'customer.profile_updated.v1', 'actor_id' => $admin->id]);
    }
}
