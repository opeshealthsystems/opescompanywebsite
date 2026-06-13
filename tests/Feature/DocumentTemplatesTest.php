<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DocumentTemplatesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_document_templates_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('document_templates'));
    }

    public function test_documents_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('documents'));
    }

    public function test_document_template_can_be_created(): void
    {
        $template = DocumentTemplate::create([
            'name'      => 'Standard Receipt',
            'type'      => 'receipt',
            'body'      => '<h1>Receipt for {{customer_name}}</h1><p>Amount: {{amount}}</p>',
            'variables' => ['customer_name', 'amount'],
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('document_templates', [
            'name' => 'Standard Receipt',
            'type' => 'receipt',
        ]);
        $this->assertEquals(['customer_name', 'amount'], $template->variables);
    }

    public function test_document_can_be_issued_to_user(): void
    {
        $admin    = User::factory()->create();
        $admin->assignRole('admin');
        $customer = User::factory()->create(['name' => 'Ambe John']);
        $customer->assignRole('customer');

        $template = DocumentTemplate::create([
            'name'      => 'Receipt Template',
            'type'      => 'receipt',
            'body'      => '<p>Receipt for {{customer_name}}</p>',
            'variables' => ['customer_name'],
            'is_active' => true,
        ]);

        $document = Document::create([
            'document_template_id' => $template->id,
            'type'                 => 'receipt',
            'title'                => 'Payment Receipt',
            'reference_number'     => 'RCT-2026-00001',
            'body_rendered'        => '<p>Receipt for Ambe John</p>',
            'issued_by'            => $admin->id,
            'addressee_user_id'    => $customer->id,
            'addressee_name'       => $customer->name,
            'addressee_email'      => $customer->email,
            'status'               => 'sent',
            'requires_signature'   => false,
        ]);

        $this->assertDatabaseHas('documents', [
            'reference_number'  => 'RCT-2026-00001',
            'addressee_user_id' => $customer->id,
            'status'            => 'sent',
        ]);

        $this->assertEquals($template->id, $document->template->id);
        $this->assertEquals($customer->id, $document->addressee->id);
        $this->assertEquals($admin->id, $document->issuer->id);
    }

    public function test_manage_documents_permission_exists(): void
    {
        $this->assertDatabaseHas('permissions', ['name' => 'manage_documents']);
    }

    public function test_admin_has_manage_documents_permission(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->assertTrue($admin->hasPermissionTo('manage_documents'));
    }

    public function test_document_reference_number_auto_generates_correctly(): void
    {
        $refReceipt = Document::generateReferenceNumber('receipt');
        $this->assertStringStartsWith('RCT-' . now()->year, $refReceipt);

        $refContract = Document::generateReferenceNumber('contract_employee');
        $this->assertStringStartsWith('EMP-CNT-' . now()->year, $refContract);
    }

    public function test_document_template_renders_variables(): void
    {
        $template = DocumentTemplate::create([
            'name'      => 'Test Template',
            'type'      => 'receipt',
            'body'      => '<p>Hello {{customer_name}}, amount: {{amount}}</p>',
            'variables' => ['customer_name', 'amount'],
            'is_active' => true,
        ]);

        $rendered = Document::renderTemplate($template, [
            'customer_name' => 'Dr. Ambe',
            'amount'        => '150,000',
        ]);

        $this->assertStringContainsString('Dr. Ambe', $rendered);
        $this->assertStringContainsString('150,000', $rendered);
        $this->assertStringNotContainsString('{{customer_name}}', $rendered);
    }

    public function test_document_signing_token_is_valid_when_pending(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $template = DocumentTemplate::create([
            'name'      => 'Contract',
            'type'      => 'contract_business',
            'body'      => '<p>Contract body</p>',
            'variables' => [],
            'is_active' => true,
        ]);

        $document = Document::create([
            'document_template_id'       => $template->id,
            'type'                       => 'contract_business',
            'title'                      => 'Test Contract',
            'reference_number'           => 'BSN-CNT-2026-00001',
            'body_rendered'              => '<p>Contract body</p>',
            'issued_by'                  => $admin->id,
            'addressee_name'             => 'Dr. Ambe',
            'status'                     => 'pending_signature',
            'requires_signature'         => true,
            'signature_token'            => 'test-token-abc123',
            'signature_token_expires_at' => now()->addDays(30),
        ]);

        $this->assertTrue($document->isSigningTokenValid());
    }

    public function test_signing_page_loads_with_valid_token(): void
    {
        $admin    = User::factory()->create();
        $admin->assignRole('admin');
        $template = DocumentTemplate::create([
            'name' => 'T', 'type' => 'contract_business',
            'body' => '<p>Body</p>', 'variables' => [], 'is_active' => true,
        ]);
        $document = Document::create([
            'document_template_id'       => $template->id,
            'type'                       => 'contract_business',
            'title'                      => 'Test Contract',
            'reference_number'           => 'BSN-CNT-2026-00099',
            'body_rendered'              => '<p>Body</p>',
            'issued_by'                  => $admin->id,
            'addressee_name'             => 'Ambe John',
            'status'                     => 'pending_signature',
            'requires_signature'         => true,
            'signature_token'            => 'validtoken999',
            'signature_token_expires_at' => now()->addDays(30),
        ]);

        $response = $this->get('/documents/validtoken999/sign');
        $response->assertOk();
        $response->assertSee('Ambe John');
    }

    public function test_document_can_be_signed_via_token(): void
    {
        $admin    = User::factory()->create();
        $admin->assignRole('admin');
        $template = DocumentTemplate::create([
            'name' => 'T2', 'type' => 'contract_business',
            'body' => '<p>Body</p>', 'variables' => [], 'is_active' => true,
        ]);
        $document = Document::create([
            'document_template_id'       => $template->id,
            'type'                       => 'contract_business',
            'title'                      => 'Contract',
            'reference_number'           => 'BSN-CNT-2026-00098',
            'body_rendered'              => '<p>Body</p>',
            'issued_by'                  => $admin->id,
            'addressee_name'             => 'Dr. Ambe',
            'status'                     => 'pending_signature',
            'requires_signature'         => true,
            'signature_token'            => 'signtoken456',
            'signature_token_expires_at' => now()->addDays(30),
        ]);

        $response = $this->post('/documents/signtoken456/sign', [
            'typed_name' => 'Dr. Ambe John',
        ]);

        $response->assertRedirect();
        $document->refresh();
        $this->assertEquals('signed', $document->status);
        $this->assertEquals('Dr. Ambe John', $document->signed_by_name);
        $this->assertNotNull($document->signed_at);
        $this->assertNull($document->signature_token);
    }
}
