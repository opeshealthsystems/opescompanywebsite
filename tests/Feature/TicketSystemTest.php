<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TicketSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_tickets_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('tickets'));
    }

    public function test_ticket_replies_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('ticket_replies'));
    }

    public function test_ticket_can_be_created_by_customer(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $ticket = Ticket::create([
            'user_id'     => $customer->id,
            'subject'     => 'Cannot access my dashboard',
            'description' => 'I get a 403 error when I log in.',
            'type'        => 'support',
            'priority'    => 'medium',
            'status'      => 'open',
        ]);

        $this->assertDatabaseHas('tickets', [
            'subject' => 'Cannot access my dashboard',
            'status'  => 'open',
            'user_id' => $customer->id,
        ]);

        $this->assertMatchesRegularExpression('/^TKT-\d{4}-\d{5}$/', $ticket->reference_number);
        $this->assertEquals($customer->id, $ticket->customer->id);
    }

    public function test_ticket_reply_can_be_added(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $support  = User::factory()->create();
        $support->assignRole('support');

        $ticket = Ticket::create([
            'user_id'     => $customer->id,
            'subject'     => 'Test ticket',
            'description' => 'Test description',
            'type'        => 'support',
            'priority'    => 'low',
            'status'      => 'open',
        ]);

        $reply = TicketReply::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $support->id,
            'body'        => 'We are looking into this.',
            'is_internal' => false,
        ]);

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'body'      => 'We are looking into this.',
        ]);

        $this->assertEquals($ticket->id, $reply->ticket->id);
        $this->assertEquals($support->id, $reply->author->id);
    }

    public function test_ticket_reference_number_increments(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $t1 = Ticket::create([
            'user_id'     => $customer->id,
            'subject'     => 'First ticket',
            'description' => 'First',
            'type'        => 'support',
            'priority'    => 'low',
            'status'      => 'open',
        ]);

        $t2 = Ticket::create([
            'user_id'     => $customer->id,
            'subject'     => 'Second ticket',
            'description' => 'Second',
            'type'        => 'support',
            'priority'    => 'low',
            'status'      => 'open',
        ]);

        $this->assertNotEquals($t1->reference_number, $t2->reference_number);
    }

    public function test_manage_tickets_permission_exists(): void
    {
        $this->assertDatabaseHas('permissions', ['name' => 'manage_tickets']);
    }

    public function test_support_has_manage_tickets_permission(): void
    {
        $support = User::factory()->create();
        $support->assignRole('support');
        $this->assertTrue($support->hasPermissionTo('manage_tickets'));
    }

    public function test_customer_can_view_their_tickets(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        Ticket::create([
            'user_id'     => $customer->id,
            'subject'     => 'My Billing Issue',
            'description' => 'I was charged twice.',
            'type'        => 'billing',
            'priority'    => 'high',
            'status'      => 'open',
        ]);

        $this->actingAs($customer)
            ->get('/en/customer/tickets')
            ->assertOk()
            ->assertSee('My Billing Issue');
    }

    public function test_customer_cannot_view_another_customers_ticket(): void
    {
        $customer1 = User::factory()->create();
        $customer1->assignRole('customer');
        $customer2 = User::factory()->create();
        $customer2->assignRole('customer');

        $ticket = Ticket::create([
            'user_id'     => $customer1->id,
            'subject'     => 'Private Ticket',
            'description' => 'Private content.',
            'type'        => 'support',
            'priority'    => 'low',
            'status'      => 'open',
        ]);

        $this->actingAs($customer2)
            ->get('/en/customer/tickets/' . $ticket->id)
            ->assertForbidden();
    }

    public function test_customer_can_create_ticket_via_form(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)
            ->post('/en/customer/tickets', [
                'subject'     => 'Test Support Request',
                'description' => 'I need help with something.',
                'type'        => 'support',
                'priority'    => 'medium',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'subject' => 'Test Support Request',
            'user_id' => $customer->id,
            'status'  => 'open',
        ]);
    }
}
