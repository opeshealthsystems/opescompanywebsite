<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    private function makeTicket(int $userId, string $status = 'open'): Ticket
    {
        return Ticket::create([
            'user_id'     => $userId,
            'subject'     => 'Test Ticket',
            'description' => 'Test description.',
            'type'        => 'support',
            'priority'    => 'low',
            'status'      => $status,
        ]);
    }

    public function test_owner_can_view_own_ticket(): void
    {
        $customer = $this->makeUser('customer');
        $ticket = $this->makeTicket($customer->id);
        $this->assertTrue($customer->can('view', $ticket));
    }

    public function test_non_owner_customer_cannot_view_ticket(): void
    {
        $owner = $this->makeUser('customer');
        $other = $this->makeUser('customer');
        $ticket = $this->makeTicket($owner->id);
        $this->assertFalse($other->can('view', $ticket));
    }

    public function test_support_can_view_any_ticket(): void
    {
        $owner = $this->makeUser('customer');
        $support = $this->makeUser('support');
        $ticket = $this->makeTicket($owner->id);
        $this->assertTrue($support->can('view', $ticket));
    }

    public function test_support_can_update_status(): void
    {
        $owner = $this->makeUser('customer');
        $support = $this->makeUser('support');
        $ticket = $this->makeTicket($owner->id);
        $this->assertTrue($support->can('updateStatus', $ticket));
    }

    public function test_customer_cannot_update_status(): void
    {
        $customer = $this->makeUser('customer');
        $ticket = $this->makeTicket($customer->id);
        $this->assertFalse($customer->can('updateStatus', $ticket));
    }

    public function test_customer_ticket_show_uses_policy(): void
    {
        $owner = $this->makeUser('customer');
        $other = $this->makeUser('customer');
        $ticket = $this->makeTicket($owner->id, 'open');

        $response = $this->actingAs($other)->get("/en/customer/tickets/{$ticket->id}");
        $response->assertStatus(403);
    }

    public function test_owner_can_view_ticket_page(): void
    {
        $owner = $this->makeUser('customer');
        $ticket = $this->makeTicket($owner->id, 'open');

        $response = $this->actingAs($owner)->get("/en/customer/tickets/{$ticket->id}");
        $response->assertStatus(200);
    }
}
