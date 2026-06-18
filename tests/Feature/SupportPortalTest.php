<?php
namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function supportUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('support');
        return $user;
    }

    public function test_support_dashboard_loads(): void
    {
        $support = $this->supportUser();

        Ticket::create([
            'assigned_to' => $support->id,
            'subject'     => 'Cannot log in',
            'description' => 'Customer reports login failure',
            'type'        => 'support',
            'status'      => 'open',
            'priority'    => 'high',
        ]);

        $this->actingAs($support)
            ->get(route('support.dashboard', ['locale' => 'en']))
            ->assertOk()
            ->assertViewHas('myOpenCount', 1)
            ->assertViewHas('myResolvedToday');
    }

    public function test_non_support_cannot_access_support_portal(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)
            ->get(route('support.dashboard', ['locale' => 'en']))
            ->assertForbidden();
    }
}
