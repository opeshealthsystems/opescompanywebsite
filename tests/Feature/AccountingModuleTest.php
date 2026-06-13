<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AccountingModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_invoices_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('invoices'));
    }

    public function test_invoice_items_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('invoice_items'));
    }

    public function test_invoice_can_be_created_with_items(): void
    {
        $admin    = User::factory()->create();
        $admin->assignRole('admin');
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'issued_by'   => $admin->id,
            'status'      => 'draft',
            'currency'    => 'XAF',
            'tax_rate'    => 0,
            'due_date'    => now()->addDays(30)->toDateString(),
        ]);

        $invoice->items()->create([
            'description' => 'OPESCare Annual License',
            'quantity'    => 1,
            'unit_price'  => 500000,
            'total'       => 500000,
        ]);

        $this->assertDatabaseHas('invoices', [
            'customer_id' => $customer->id,
            'status'      => 'draft',
        ]);
        $this->assertDatabaseHas('invoice_items', [
            'description' => 'OPESCare Annual License',
            'unit_price'  => 500000,
        ]);
        $this->assertEquals(1, $invoice->items()->count());
    }

    public function test_invoice_number_is_auto_generated(): void
    {
        $admin    = User::factory()->create();
        $customer = User::factory()->create();

        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'issued_by'   => $admin->id,
            'status'      => 'draft',
            'currency'    => 'XAF',
            'tax_rate'    => 0,
            'due_date'    => now()->addDays(30)->toDateString(),
        ]);

        $this->assertMatchesRegularExpression(
            '/^INV-\d{4}-\d{5}$/',
            $invoice->invoice_number
        );
    }

    public function test_invoice_number_increments_sequentially(): void
    {
        $admin    = User::factory()->create();
        $customer = User::factory()->create();

        $first  = Invoice::create(['customer_id' => $customer->id, 'issued_by' => $admin->id, 'status' => 'draft', 'currency' => 'XAF', 'tax_rate' => 0, 'due_date' => now()->addDays(30)->toDateString()]);
        $second = Invoice::create(['customer_id' => $customer->id, 'issued_by' => $admin->id, 'status' => 'draft', 'currency' => 'XAF', 'tax_rate' => 0, 'due_date' => now()->addDays(30)->toDateString()]);

        $year = now()->year;
        $this->assertEquals("INV-{$year}-00001", $first->invoice_number);
        $this->assertEquals("INV-{$year}-00002", $second->invoice_number);
    }

    public function test_invoice_subtotal_is_computed(): void
    {
        $admin    = User::factory()->create();
        $customer = User::factory()->create();

        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'issued_by'   => $admin->id,
            'status'      => 'draft',
            'currency'    => 'XAF',
            'tax_rate'    => 10,
            'due_date'    => now()->addDays(30)->toDateString(),
        ]);

        $invoice->items()->createMany([
            ['description' => 'Item A', 'quantity' => 2, 'unit_price' => 100000, 'total' => 200000],
            ['description' => 'Item B', 'quantity' => 1, 'unit_price' => 50000,  'total' => 50000],
        ]);

        $invoice->load('items');

        $this->assertEquals(250000, $invoice->subtotal);
        $this->assertEquals(25000,  $invoice->taxAmount);
        $this->assertEquals(275000, $invoice->grandTotal);
    }

    public function test_manage_accounting_permission_exists(): void
    {
        $this->assertDatabaseHas('permissions', ['name' => 'manage_accounting']);
    }

    public function test_admin_has_manage_accounting_permission(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->assertTrue($admin->hasPermissionTo('manage_accounting'));
    }

    public function test_customer_can_view_their_invoices(): void
    {
        $admin    = User::factory()->create();
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        Invoice::create([
            'customer_id' => $customer->id,
            'issued_by'   => $admin->id,
            'status'      => 'sent',
            'currency'    => 'XAF',
            'tax_rate'    => 0,
            'due_date'    => now()->addDays(30)->toDateString(),
        ]);

        $this->actingAs($customer)
            ->get('/en/customer/invoices')
            ->assertOk();
    }

    public function test_customer_cannot_view_another_customers_invoice(): void
    {
        $admin     = User::factory()->create();
        $customer1 = User::factory()->create();
        $customer1->assignRole('customer');
        $customer2 = User::factory()->create();
        $customer2->assignRole('customer');

        $invoice = Invoice::create([
            'customer_id' => $customer1->id,
            'issued_by'   => $admin->id,
            'status'      => 'sent',
            'currency'    => 'XAF',
            'tax_rate'    => 0,
            'due_date'    => now()->addDays(30)->toDateString(),
        ]);

        $this->actingAs($customer2)
            ->get('/en/customer/invoices/' . $invoice->id)
            ->assertForbidden();
    }
}
