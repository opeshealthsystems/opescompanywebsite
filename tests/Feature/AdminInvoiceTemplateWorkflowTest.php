<?php

namespace Tests\Feature;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\InvoiceTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Mail, Notification};
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminInvoiceTemplateWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_template_reads_and_writes_use_shared_finance_commands(): void
    {
        Mail::fake();
        Notification::fake();
        foreach (['admin', 'customer'] as $role) Role::findOrCreate($role, 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $customer = User::factory()->create(['is_active' => true]);
        $customer->assignRole('customer');
        $this->actingAs($admin);
        $data = [
            'name' => 'Fictional recurring invoice fixture', 'customer_id' => $customer->id, 'client_name' => null, 'client_email' => null,
            'frequency' => 'monthly', 'next_due_date' => today()->toDateString(), 'end_date' => null,
            'payment_terms_days' => 30, 'currency' => 'XAF', 'tax_rate' => 0, 'is_active' => true, 'issued_by' => $admin->id,
            'line_items' => [['description' => 'Fictional service fixture', 'quantity' => 1, 'unit_price' => 1500]], 'notes' => null,
        ];

        AdminWorkflowAdapter::run('admin.finance.invoiceTemplates.create', $data);
        $template = InvoiceTemplate::query()->where('name', $data['name'])->firstOrFail();
        $this->assertSame(1, count($template->line_items));
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.invoice_template_created.v1', 'actor_id' => $admin->id]);

        $result = app(WorkflowDispatcher::class)->execute('admin.finance.invoiceTemplates.index', new WorkflowInput(['search' => 'recurring'], actor: $admin));
        $this->assertSame($template->id, $result->data['items'][0]['id']);

        AdminWorkflowAdapter::run('admin.finance.invoiceTemplates.update', [...$data, 'name' => 'Updated fictional recurring invoice fixture', 'is_active' => false], ['invoiceTemplate' => $template->id]);
        $this->assertDatabaseHas('invoice_templates', ['id' => $template->id, 'name' => 'Updated fictional recurring invoice fixture', 'is_active' => false]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.invoice_template_updated.v1', 'actor_id' => $admin->id]);
    }
}
