<?php

namespace Tests\Feature;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function customer(): User
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        return $user;
    }

    public function test_customer_can_load_index_and_create_pages(): void
    {
        $customer = $this->customer();

        $this->actingAs($customer)
            ->get('/en/customer/service-requests')
            ->assertOk();

        $this->actingAs($customer)
            ->get('/en/customer/service-requests/create')
            ->assertOk();
    }

    public function test_customer_can_create_a_service_request(): void
    {
        $customer = $this->customer();

        $this->actingAs($customer)
            ->post('/en/customer/service-requests', [
                'type'           => 'installation',
                'description'    => 'Please install the new monitoring units.',
                'preferred_date' => now()->addWeek()->toDateString(),
                'preferred_time' => '10:00',
                'location'       => 'Central Hospital Douala',
            ])
            ->assertRedirect('/en/customer/service-requests')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('service_requests', [
            'type'        => 'installation',
            'customer_id' => $customer->id,
        ]);

        $request = ServiceRequest::where('customer_id', $customer->id)->first();
        $this->assertNotNull($request);
        $this->assertMatchesRegularExpression('/^SVC-\d{4}-\d{5}$/', $request->reference_number);
    }

    public function test_validation_rejects_past_preferred_date(): void
    {
        $customer = $this->customer();

        $this->actingAs($customer)
            ->post('/en/customer/service-requests', [
                'type'           => 'maintenance',
                'preferred_date' => now()->subDay()->toDateString(),
            ])
            ->assertSessionHasErrors('preferred_date');

        $this->assertDatabaseCount('service_requests', 0);
    }

    public function test_validation_rejects_missing_type(): void
    {
        $customer = $this->customer();

        $this->actingAs($customer)
            ->post('/en/customer/service-requests', [
                'preferred_date' => now()->addWeek()->toDateString(),
            ])
            ->assertSessionHasErrors('type');

        $this->assertDatabaseCount('service_requests', 0);
    }

    public function test_customer_can_view_their_own_service_request(): void
    {
        $customer = $this->customer();

        $request = ServiceRequest::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $this->actingAs($customer)
            ->get('/en/customer/service-requests/' . $request->id)
            ->assertOk()
            ->assertSee($request->reference_number);
    }

    public function test_customer_cannot_view_another_customers_service_request(): void
    {
        $owner = $this->customer();
        $other = $this->customer();

        $request = ServiceRequest::factory()->create([
            'customer_id' => $owner->id,
        ]);

        $this->actingAs($other)
            ->get('/en/customer/service-requests/' . $request->id)
            ->assertForbidden();
    }
}
