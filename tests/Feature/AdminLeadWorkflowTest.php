<?php

namespace Tests\Feature;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Mail, Notification};
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminLeadWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_admin_reads_and_writes_share_enquiry_commands(): void
    {
        Mail::fake();
        Notification::fake();
        Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $data = [
            'name' => 'Fictional CRM contact', 'email' => 'crm-contact@example.test', 'phone' => '+237600000000',
            'facility_type' => 'clinic', 'products' => 'Fictional product', 'message' => 'Fictional inquiry fixture.',
            'source' => 'demo', 'product_slug' => 'fictional-product', 'locale' => 'en', 'status' => 'new',
        ];

        AdminWorkflowAdapter::run('admin.enquiries.leads.create', $data);
        $lead = Lead::query()->where('email', $data['email'])->firstOrFail();
        $this->assertSame('new', $lead->status);
        $this->assertDatabaseHas('business_outbox', ['type' => 'enquiries.lead_created.v1', 'actor_id' => $admin->id]);

        $result = app(WorkflowDispatcher::class)->execute('admin.enquiries.leads.index', new WorkflowInput(
            ['search' => 'crm-contact@example.test'], actor: $admin,
        ));
        $this->assertSame($lead->id, $result->data['items'][0]['id']);

        AdminWorkflowAdapter::run('admin.enquiries.leads.update', [...$data, 'name' => 'Updated fictional CRM contact', 'status' => 'contacted'], ['lead' => $lead->id]);
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'name' => 'Updated fictional CRM contact', 'status' => 'contacted']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'enquiries.lead_updated.v1', 'actor_id' => $admin->id]);

        AdminWorkflowAdapter::run('admin.enquiries.leads.delete', parameters: ['lead' => $lead->id]);
        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }
}
