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

    public function test_support_can_view_ticket_list(): void
    {
        $support = $this->supportUser();
        Ticket::create([
            'assigned_to' => $support->id,
            'subject'     => 'Screen flicker',
            'description' => 'Dashboard flickers on load',
            'type'        => 'support',
            'status'      => 'open',
            'priority'    => 'medium',
        ]);

        $this->actingAs($support)
            ->get(route('support.tickets', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Screen flicker');
    }

    public function test_support_can_add_reply(): void
    {
        $support = $this->supportUser();
        $ticket  = Ticket::create([
            'assigned_to' => $support->id,
            'subject'     => 'Password reset broken',
            'description' => 'User cannot reset password',
            'type'        => 'support',
            'status'      => 'open',
            'priority'    => 'high',
        ]);

        $this->actingAs($support)
            ->post(route('support.tickets.reply', ['locale' => 'en', 'ticket' => $ticket->id]), [
                'body'        => 'We are looking into this now.',
                'is_internal' => false,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'user_id'   => $support->id,
            'body'      => 'We are looking into this now.',
        ]);
    }
}
