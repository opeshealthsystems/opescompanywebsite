<?php

namespace Tests\Feature;

use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Mail, Notification};
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminSupportWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_filament_ticket_create_and_update_use_the_shared_admin_commands(): void
    {
        Mail::fake();
        Notification::fake();
        foreach (['admin', 'customer', 'support'] as $role) Role::findOrCreate($role, 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $customer = User::factory()->create(['is_active' => true]);
        $customer->assignRole('customer');
        $this->actingAs($admin);

        AdminWorkflowAdapter::run('admin.support.tickets.create', [
            'user_id' => $customer->id,
            'subject' => 'Fictional API-first support ticket',
            'description' => 'Fictional support workflow fixture.',
            'type' => 'support',
            'priority' => 'medium',
        ]);
        $ticket = Ticket::query()->where('subject', 'Fictional API-first support ticket')->firstOrFail();

        $this->assertSame('open', $ticket->status);
        $this->assertNotEmpty($ticket->reference_number);
        $this->assertDatabaseHas('business_outbox', ['type' => 'support.ticket_created.v1', 'actor_id' => $admin->id]);

        AdminWorkflowAdapter::run('admin.support.tickets.update', [
            'user_id' => $customer->id,
            'subject' => 'Updated fictional support ticket',
            'description' => 'Updated fictional details.',
            'type' => 'technical',
            'priority' => 'high',
            'resolution' => null,
        ], ['ticket' => $ticket->id]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'subject' => 'Updated fictional support ticket',
            'type' => 'technical',
            'priority' => 'high',
        ]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'support.ticket_details_updated.v1', 'actor_id' => $admin->id]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        AdminWorkflowAdapter::record('admin.support.tickets.create', [
            'user_id' => $admin->id,
            'subject' => 'Invalid customer assignment',
            'description' => 'This is deliberately invalid.',
            'type' => 'support',
            'priority' => 'low',
        ]);
    }
}
