# Document Templates — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a complete document management system — receipts, letterheads, and digital contracts (employee + business) — with PDF generation, browser view, and a digital signature workflow where recipients can sign documents via a secure token link.

**Architecture:** Two-tier: an admin layer (Filament resources) where staff draft, issue, and track documents from reusable templates; and a customer/employee portal layer where recipients view, download, and sign their documents. Documents are stored as rendered HTML (template variables replaced at issue time) so the PDF always reflects the exact content at the time of issuance, not the current template. Digital signing uses a single-use UUID token (expires 30 days) that creates a public signing page — no login required, signed data is stored as a JSON blob (typed name + timestamp + IP). PDF generation uses `barryvdh/laravel-dompdf`.

**Document Types:** `receipt` | `letterhead` | `contract_employee` | `contract_business`

**Reference Numbers:** Auto-generated at issuance — `RCT-2026-00001`, `LTH-2026-00001`, `EMP-CNT-2026-00001`, `BSN-CNT-2026-00001`

**Status Flow:** `draft` → `sent` → `pending_signature` (if signature required) → `signed` | `voided`

**Tech Stack:** Laravel 13, PHP 8.3, Filament v3.3, barryvdh/laravel-dompdf, Blade templates, plain JS canvas for signature capture, Tailwind CSS v4, PHPUnit / SQLite in-memory

---

## File Map

### New files
- `database/migrations/2026_06_13_220000_create_document_templates_table.php`
- `database/migrations/2026_06_13_221000_create_documents_table.php`
- `database/seeders/DocumentTemplateSeeder.php`
- `app/Models/DocumentTemplate.php`
- `app/Models/Document.php`
- `app/Filament/Resources/DocumentTemplateResource.php`
- `app/Filament/Resources/DocumentTemplateResource/Pages/ListDocumentTemplates.php`
- `app/Filament/Resources/DocumentTemplateResource/Pages/CreateDocumentTemplate.php`
- `app/Filament/Resources/DocumentTemplateResource/Pages/EditDocumentTemplate.php`
- `app/Filament/Resources/DocumentResource.php`
- `app/Filament/Resources/DocumentResource/Pages/ListDocuments.php`
- `app/Filament/Resources/DocumentResource/Pages/CreateDocument.php`
- `app/Filament/Resources/DocumentResource/Pages/ViewDocument.php`
- `app/Http/Controllers/Customer/DocumentController.php`
- `app/Http/Controllers/DocumentSigningController.php`
- `resources/views/documents/pdf.blade.php`
- `resources/views/documents/sign.blade.php`
- `resources/views/customer/documents/index.blade.php`
- `resources/views/customer/documents/show.blade.php`
- `tests/Feature/DocumentTemplatesTest.php`

### Modified files
- `database/seeders/DatabaseSeeder.php` — add DocumentTemplateSeeder
- `database/seeders/RolePermissionSeeder.php` — add `manage_documents` permission
- `routes/web.php` — add `/documents/{token}/sign` route + customer document routes
- `resources/css/app.css` — add document/signing CSS

---

## Task 1: Migrations + Models + Seeder

**Files:**
- Create: `database/migrations/2026_06_13_220000_create_document_templates_table.php`
- Create: `database/migrations/2026_06_13_221000_create_documents_table.php`
- Create: `app/Models/DocumentTemplate.php`
- Create: `app/Models/Document.php`
- Create: `database/seeders/DocumentTemplateSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Modify: `database/seeders/RolePermissionSeeder.php`

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/DocumentTemplatesTest.php`:

```php
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
}
```

- [ ] **Step 2: Run the tests — expect FAIL**

```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/DocumentTemplatesTest.php
```

Expected: FAIL — tables don't exist.

- [ ] **Step 3: Create `database/migrations/2026_06_13_220000_create_document_templates_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['receipt', 'letterhead', 'contract_employee', 'contract_business']);
            $table->longText('body');
            $table->json('variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
```

- [ ] **Step 4: Create `database/migrations/2026_06_13_221000_create_documents_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_template_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['receipt', 'letterhead', 'contract_employee', 'contract_business']);
            $table->string('title');
            $table->string('reference_number')->unique();
            $table->longText('body_rendered');
            $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('addressee_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('addressee_name');
            $table->string('addressee_email')->nullable();
            $table->enum('status', ['draft', 'sent', 'pending_signature', 'signed', 'voided'])->default('draft');
            $table->boolean('requires_signature')->default(false);
            $table->string('signature_token', 64)->nullable()->unique();
            $table->timestamp('signature_token_expires_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('signed_by_name')->nullable();
            $table->string('signed_ip', 45)->nullable();
            $table->json('signed_data')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
```

- [ ] **Step 5: Create `app/Models/DocumentTemplate.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentTemplate extends Model
{
    protected $fillable = [
        'name', 'type', 'body', 'variables', 'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'receipt'            => 'Receipt',
            'letterhead'         => 'Letterhead',
            'contract_employee'  => 'Employee Contract',
            'contract_business'  => 'Business Contract',
            default              => ucfirst($type),
        };
    }

    public static function referencePrefix(string $type): string
    {
        return match ($type) {
            'receipt'            => 'RCT',
            'letterhead'         => 'LTH',
            'contract_employee'  => 'EMP-CNT',
            'contract_business'  => 'BSN-CNT',
            default              => 'DOC',
        };
    }
}
```

- [ ] **Step 6: Create `app/Models/Document.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Document extends Model
{
    protected $fillable = [
        'document_template_id', 'type', 'title', 'reference_number',
        'body_rendered', 'issued_by', 'addressee_user_id',
        'addressee_name', 'addressee_email', 'status',
        'requires_signature', 'signature_token', 'signature_token_expires_at',
        'signed_at', 'signed_by_name', 'signed_ip', 'signed_data',
        'valid_until', 'notes',
    ];

    protected $casts = [
        'requires_signature'        => 'boolean',
        'signature_token_expires_at'=> 'datetime',
        'signed_at'                 => 'datetime',
        'signed_data'               => 'array',
        'valid_until'               => 'date',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function addressee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'addressee_user_id');
    }

    public static function generateReferenceNumber(string $type): string
    {
        $prefix = DocumentTemplate::referencePrefix($type);
        $year   = now()->year;

        $last = static::where('type', $type)
            ->whereYear('created_at', $year)
            ->orderByDesc('id')
            ->value('reference_number');

        $seq = 1;
        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return "{$prefix}-{$year}-" . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    public function generateSignatureToken(): void
    {
        $this->update([
            'signature_token'            => Str::random(64),
            'signature_token_expires_at' => now()->addDays(30),
            'status'                     => 'pending_signature',
        ]);
    }

    public function renderTemplate(DocumentTemplate $template, array $variables): string
    {
        $body = $template->body;
        foreach ($variables as $key => $value) {
            $body = str_replace("{{" . $key . "}}", e($value), $body);
        }
        return $body;
    }

    public function isSigned(): bool
    {
        return $this->status === 'signed' && $this->signed_at !== null;
    }

    public function isSigningTokenValid(): bool
    {
        return $this->signature_token !== null
            && $this->signature_token_expires_at !== null
            && $this->signature_token_expires_at->isFuture()
            && $this->status === 'pending_signature';
    }
}
```

- [ ] **Step 7: Create `database/seeders/DocumentTemplateSeeder.php`**

```php
<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use Illuminate\Database\Seeder;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name'      => 'OPES Standard Receipt',
                'type'      => 'receipt',
                'variables' => ['customer_name', 'customer_email', 'item_description', 'amount', 'currency', 'payment_date', 'reference_number'],
                'body'      => $this->receiptBody(),
            ],
            [
                'name'      => 'OPES Official Letterhead',
                'type'      => 'letterhead',
                'variables' => ['recipient_name', 'recipient_address', 'subject', 'body', 'date', 'sender_name', 'sender_position'],
                'body'      => $this->letterheadBody(),
            ],
            [
                'name'      => 'Employee Contract (Standard)',
                'type'      => 'contract_employee',
                'variables' => ['employee_name', 'employee_id', 'position', 'department', 'start_date', 'salary', 'currency', 'location', 'contract_date'],
                'body'      => $this->employeeContractBody(),
            ],
            [
                'name'      => 'Software License Agreement',
                'type'      => 'contract_business',
                'variables' => ['business_name', 'business_address', 'contact_name', 'software_name', 'license_type', 'seats', 'start_date', 'end_date', 'amount', 'currency', 'contract_date'],
                'body'      => $this->businessContractBody(),
            ],
        ];

        foreach ($templates as $data) {
            DocumentTemplate::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['is_active' => true])
            );
        }
    }

    private function receiptBody(): string
    {
        return <<<'HTML'
<div class="doc-page">
  <div class="doc-header">
    <div class="doc-logo-block">
      <h1 class="doc-company">OPES Health Systems SARL</h1>
      <p class="doc-company-sub">Digital Healthcare Solutions | Douala, Cameroon</p>
      <p class="doc-company-sub">Email: billing@opeshealthsystems.com | Tel: +237 600 000 000</p>
    </div>
    <div class="doc-stamp">
      <div class="doc-stamp-label">RECEIPT</div>
      <div class="doc-stamp-ref">{{reference_number}}</div>
    </div>
  </div>
  <div class="doc-divider"></div>
  <div class="doc-meta-row">
    <div>
      <p class="doc-meta-label">BILLED TO</p>
      <p class="doc-meta-value">{{customer_name}}</p>
      <p class="doc-meta-value">{{customer_email}}</p>
    </div>
    <div>
      <p class="doc-meta-label">PAYMENT DATE</p>
      <p class="doc-meta-value">{{payment_date}}</p>
    </div>
  </div>
  <table class="doc-table">
    <thead>
      <tr><th>Description</th><th class="text-right">Amount</th></tr>
    </thead>
    <tbody>
      <tr><td>{{item_description}}</td><td class="text-right">{{currency}} {{amount}}</td></tr>
    </tbody>
    <tfoot>
      <tr class="doc-total-row"><td>TOTAL PAID</td><td class="text-right">{{currency}} {{amount}}</td></tr>
    </tfoot>
  </table>
  <div class="doc-footer-note">
    <p>Thank you for your payment. This receipt serves as confirmation of your transaction with OPES Health Systems SARL.</p>
    <p>For queries, contact billing@opeshealthsystems.com</p>
  </div>
  <div class="doc-signature-block">
    <div class="doc-sig-line"></div>
    <p class="doc-sig-label">Authorised Signatory — OPES Health Systems SARL</p>
  </div>
</div>
HTML;
    }

    private function letterheadBody(): string
    {
        return <<<'HTML'
<div class="doc-page">
  <div class="doc-header">
    <div class="doc-logo-block">
      <h1 class="doc-company">OPES Health Systems SARL</h1>
      <p class="doc-company-sub">Digital Healthcare Solutions | Douala, Cameroon</p>
      <p class="doc-company-sub">Email: info@opeshealthsystems.com | Tel: +237 600 000 000</p>
    </div>
  </div>
  <div class="doc-divider"></div>
  <div style="margin-top:2rem">
    <p class="doc-date">{{date}}</p>
    <p class="doc-recipient">{{recipient_name}}</p>
    <p class="doc-recipient-addr">{{recipient_address}}</p>
    <p class="doc-subject"><strong>RE: {{subject}}</strong></p>
    <div class="doc-body-text">
      {{body}}
    </div>
  </div>
  <div class="doc-sign-section" style="margin-top:3rem">
    <p>Yours sincerely,</p>
    <div class="doc-sig-line" style="margin:2rem 0 0.5rem"></div>
    <p><strong>{{sender_name}}</strong></p>
    <p class="doc-meta-value">{{sender_position}}</p>
    <p class="doc-meta-value">OPES Health Systems SARL</p>
  </div>
</div>
HTML;
    }

    private function employeeContractBody(): string
    {
        return <<<'HTML'
<div class="doc-page">
  <div class="doc-header">
    <div class="doc-logo-block">
      <h1 class="doc-company">OPES Health Systems SARL</h1>
      <p class="doc-company-sub">Digital Healthcare Solutions | Douala, Cameroon</p>
    </div>
    <div class="doc-stamp">
      <div class="doc-stamp-label">EMPLOYMENT CONTRACT</div>
    </div>
  </div>
  <div class="doc-divider"></div>
  <h2 class="doc-section-title">CONTRACT OF EMPLOYMENT</h2>
  <p class="doc-body-text">This Employment Contract is entered into on <strong>{{contract_date}}</strong> between:</p>
  <p class="doc-body-text"><strong>EMPLOYER:</strong> OPES Health Systems SARL, a company incorporated under the laws of Cameroon, having its registered office in Douala, Cameroon ("the Company").</p>
  <p class="doc-body-text"><strong>EMPLOYEE:</strong> <strong>{{employee_name}}</strong> (Employee ID: {{employee_id}}), hereinafter referred to as "the Employee".</p>

  <h3 class="doc-clause-title">1. Position and Duties</h3>
  <p class="doc-body-text">The Employee is appointed to the position of <strong>{{position}}</strong> in the <strong>{{department}}</strong> department. The Employee shall perform such duties as may be assigned from time to time by management.</p>

  <h3 class="doc-clause-title">2. Commencement Date</h3>
  <p class="doc-body-text">Employment shall commence on <strong>{{start_date}}</strong>.</p>

  <h3 class="doc-clause-title">3. Place of Work</h3>
  <p class="doc-body-text">The Employee's place of work shall be <strong>{{location}}</strong>, or at such other location as required by the Company.</p>

  <h3 class="doc-clause-title">4. Remuneration</h3>
  <p class="doc-body-text">The Employee shall receive a monthly salary of <strong>{{currency}} {{salary}}</strong>, payable on the last working day of each month, subject to applicable taxes and deductions.</p>

  <h3 class="doc-clause-title">5. Confidentiality</h3>
  <p class="doc-body-text">The Employee agrees to keep confidential all proprietary information, trade secrets, and client data of the Company, both during and after employment.</p>

  <h3 class="doc-clause-title">6. Governing Law</h3>
  <p class="doc-body-text">This contract shall be governed by the Labour Code of Cameroon and any applicable legislation.</p>

  <div class="doc-signatures-row" style="margin-top:3rem">
    <div class="doc-sig-col">
      <div class="doc-sig-line"></div>
      <p><strong>{{employee_name}}</strong></p>
      <p class="doc-meta-value">Employee Signature</p>
      <p class="doc-meta-value">Date: _______________</p>
    </div>
    <div class="doc-sig-col">
      <div class="doc-sig-line"></div>
      <p><strong>Authorised Representative</strong></p>
      <p class="doc-meta-value">OPES Health Systems SARL</p>
      <p class="doc-meta-value">Date: _______________</p>
    </div>
  </div>
</div>
HTML;
    }

    private function businessContractBody(): string
    {
        return <<<'HTML'
<div class="doc-page">
  <div class="doc-header">
    <div class="doc-logo-block">
      <h1 class="doc-company">OPES Health Systems SARL</h1>
      <p class="doc-company-sub">Digital Healthcare Solutions | Douala, Cameroon</p>
    </div>
    <div class="doc-stamp">
      <div class="doc-stamp-label">SOFTWARE LICENSE AGREEMENT</div>
    </div>
  </div>
  <div class="doc-divider"></div>
  <h2 class="doc-section-title">SOFTWARE LICENSE AND SERVICE AGREEMENT</h2>
  <p class="doc-body-text">This Software License Agreement ("Agreement") is entered into on <strong>{{contract_date}}</strong> between:</p>
  <p class="doc-body-text"><strong>LICENSOR:</strong> OPES Health Systems SARL, Douala, Cameroon ("OPES").</p>
  <p class="doc-body-text"><strong>LICENSEE:</strong> <strong>{{business_name}}</strong>, located at {{business_address}}, represented by <strong>{{contact_name}}</strong> ("the Client").</p>

  <h3 class="doc-clause-title">1. Grant of License</h3>
  <p class="doc-body-text">OPES hereby grants the Client a non-exclusive, non-transferable license to use <strong>{{software_name}}</strong> (License Type: <strong>{{license_type}}</strong>) for up to <strong>{{seats}}</strong> concurrent user(s).</p>

  <h3 class="doc-clause-title">2. License Period</h3>
  <p class="doc-body-text">The license shall be valid from <strong>{{start_date}}</strong> to <strong>{{end_date}}</strong>, subject to timely renewal and payment.</p>

  <h3 class="doc-clause-title">3. Fees</h3>
  <p class="doc-body-text">The Client shall pay <strong>{{currency}} {{amount}}</strong> for the license period specified above. Payment terms and schedules shall be as agreed separately.</p>

  <h3 class="doc-clause-title">4. Restrictions</h3>
  <p class="doc-body-text">The Client may not sublicense, sell, resell, transfer, assign, or otherwise commercially exploit the Software. The Client may not reverse-engineer, decompile, or disassemble any part of the Software.</p>

  <h3 class="doc-clause-title">5. Data and Confidentiality</h3>
  <p class="doc-body-text">Patient and healthcare data processed through the Software remains the property of the Client. OPES shall implement appropriate technical and organizational measures to protect such data in accordance with Cameroonian data protection law.</p>

  <h3 class="doc-clause-title">6. Limitation of Liability</h3>
  <p class="doc-body-text">OPES shall not be liable for any indirect, incidental, or consequential damages arising from use of the Software.</p>

  <h3 class="doc-clause-title">7. Governing Law</h3>
  <p class="doc-body-text">This Agreement shall be governed by the laws of Cameroon. Any disputes shall be resolved by the courts of Douala, Cameroon.</p>

  <div class="doc-signatures-row" style="margin-top:3rem">
    <div class="doc-sig-col">
      <div class="doc-sig-line"></div>
      <p><strong>{{contact_name}}</strong></p>
      <p class="doc-meta-value">{{business_name}}</p>
      <p class="doc-meta-value">Date: _______________</p>
    </div>
    <div class="doc-sig-col">
      <div class="doc-sig-line"></div>
      <p><strong>Authorised Representative</strong></p>
      <p class="doc-meta-value">OPES Health Systems SARL</p>
      <p class="doc-meta-value">Date: _______________</p>
    </div>
  </div>
</div>
HTML;
    }
}
```

- [ ] **Step 8: Modify `database/seeders/DatabaseSeeder.php`** — add `DocumentTemplateSeeder::class` to the array.

- [ ] **Step 9: Modify `database/seeders/RolePermissionSeeder.php`** — add `'manage_documents'` to the `$permissions` array, and add it to `super_admin`, `admin` role maps.

- [ ] **Step 10: Run migrations**

```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan migrate
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan db:seed --class=DocumentTemplateSeeder
```

- [ ] **Step 11: Run the tests — expect PASS**

```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/DocumentTemplatesTest.php
```

Expected: 5 tests pass.

- [ ] **Step 12: Run full suite — no regressions**

```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 41 + 5 = 46 tests pass.

- [ ] **Step 13: Commit**

```bash
git add database/migrations/ app/Models/DocumentTemplate.php app/Models/Document.php database/seeders/ tests/Feature/DocumentTemplatesTest.php
git commit -m "feat: add document_templates and documents tables, models, seeders, and manage_documents permission"
```

---

## Task 2: Install DomPDF + PDF View

**Files:**
- Run: `composer require barryvdh/laravel-dompdf`
- Create: `resources/views/documents/pdf.blade.php`

- [ ] **Step 1: Install DomPDF**

```bash
cd C:\laragon\www\ohs
composer require barryvdh/laravel-dompdf
```

Expected: Installs without errors. DomPDF auto-discovers its service provider.

- [ ] **Step 2: Create `resources/views/documents/pdf.blade.php`**

This view is used for both browser preview and PDF generation. It must use inline CSS (no `@vite`, no external resources) since DomPDF cannot load Vite-compiled assets.

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $document->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 13px; color: #1e293b; background: #fff; }
        .doc-page { padding: 2.5rem 2.5rem 2rem; min-height: 100vh; }
        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
        .doc-company { font-size: 1.5rem; font-weight: 700; color: #0f172a; letter-spacing: -0.02em; }
        .doc-company-sub { font-size: 0.75rem; color: #64748b; margin-top: 0.2rem; }
        .doc-stamp { text-align: right; }
        .doc-stamp-label { font-size: 1rem; font-weight: 700; color: #00C896; text-transform: uppercase; letter-spacing: 0.1em; }
        .doc-stamp-ref { font-size: 0.8rem; color: #475569; margin-top: 0.25rem; }
        .doc-divider { border: none; border-top: 2px solid #00C896; margin: 1rem 0; }
        .doc-meta-row { display: flex; justify-content: space-between; margin: 1.5rem 0; }
        .doc-meta-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.06em; }
        .doc-meta-value { font-size: 0.875rem; color: #1e293b; margin-top: 0.2rem; }
        .doc-table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        .doc-table th { background: #0f172a; color: #f1f5f9; padding: 0.6rem 0.75rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: left; }
        .doc-table td { padding: 0.6rem 0.75rem; border-bottom: 1px solid #e2e8f0; font-size: 0.875rem; }
        .doc-table tfoot td { border-top: 2px solid #0f172a; font-weight: 700; padding: 0.75rem; }
        .text-right { text-align: right; }
        .doc-total-row td { background: #f8fafc; }
        .doc-footer-note { margin-top: 2rem; font-size: 0.75rem; color: #64748b; line-height: 1.6; }
        .doc-signature-block { margin-top: 3rem; }
        .doc-sig-line { border-top: 1px solid #94a3b8; width: 200px; margin-bottom: 0.5rem; }
        .doc-sig-label { font-size: 0.75rem; color: #64748b; }
        .doc-section-title { font-size: 1.1rem; font-weight: 700; text-align: center; margin: 1.5rem 0 1rem; color: #0f172a; text-transform: uppercase; letter-spacing: 0.05em; }
        .doc-clause-title { font-size: 0.9rem; font-weight: 700; margin: 1.25rem 0 0.4rem; color: #0f172a; }
        .doc-body-text { font-size: 0.875rem; line-height: 1.7; color: #334155; margin-bottom: 0.75rem; }
        .doc-date { font-size: 0.875rem; color: #64748b; margin-bottom: 1rem; }
        .doc-recipient { font-size: 0.9375rem; font-weight: 600; color: #0f172a; }
        .doc-recipient-addr { font-size: 0.8125rem; color: #64748b; margin-bottom: 0.5rem; }
        .doc-subject { font-size: 0.875rem; margin: 1.25rem 0 1.5rem; color: #0f172a; }
        .doc-signatures-row { display: flex; justify-content: space-between; margin-top: 2rem; }
        .doc-sig-col { width: 45%; }
        .doc-logo-block { flex: 1; }
        /* Signed banner */
        .doc-signed-banner {
            background: #f0fdf4; border: 2px solid #00C896; border-radius: 8px;
            padding: 1rem 1.5rem; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: 1rem;
        }
        .doc-signed-badge { color: #00C896; font-size: 1.5rem; font-weight: 700; }
        .doc-signed-details { font-size: 0.8125rem; color: #334155; }
        .doc-signed-signature { font-family: 'Georgia', serif; font-size: 1.25rem; color: #0f172a; margin-top: 0.25rem; }
        /* Print helpers */
        @media print {
            .doc-page { padding: 0; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    @if($document->isSigned())
    <div class="doc-signed-banner">
        <div class="doc-signed-badge">✓ SIGNED</div>
        <div class="doc-signed-details">
            <div>Digitally signed by <strong>{{ $document->signed_by_name }}</strong></div>
            <div>{{ $document->signed_at?->format('d M Y, H:i') }} UTC</div>
            @if(isset($document->signed_data['typed_name']))
            <div class="doc-signed-signature">{{ $document->signed_data['typed_name'] }}</div>
            @endif
        </div>
    </div>
    @endif

    {!! $document->body_rendered !!}

    <div style="margin-top:3rem; padding-top:1rem; border-top:1px solid #e2e8f0; font-size:0.7rem; color:#94a3b8; text-align:center;">
        Document Reference: {{ $document->reference_number }} | Issued by OPES Health Systems SARL
        @if($document->valid_until) | Valid until: {{ $document->valid_until->format('d M Y') }} @endif
    </div>
</body>
</html>
```

- [ ] **Step 3: Verify DomPDF is available**

```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan tinker --execute="echo class_exists(\Barryvdh\DomPDF\Facade\Pdf::class) ? 'DomPDF OK' : 'MISSING';"
```

Expected: `DomPDF OK`

- [ ] **Step 4: Run full test suite (no regressions)**

```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 46 tests pass.

- [ ] **Step 5: Commit**

```bash
git add resources/views/documents/pdf.blade.php
git commit -m "feat: install DomPDF and add PDF document view with signed banner"
```

---

## Task 3: Filament DocumentTemplateResource (Admin)

**Files:**
- Create: `app/Filament/Resources/DocumentTemplateResource.php`
- Create: `app/Filament/Resources/DocumentTemplateResource/Pages/ListDocumentTemplates.php`
- Create: `app/Filament/Resources/DocumentTemplateResource/Pages/CreateDocumentTemplate.php`
- Create: `app/Filament/Resources/DocumentTemplateResource/Pages/EditDocumentTemplate.php`

The Filament resource lets admins create and manage reusable document templates. The body editor uses a Textarea (not a rich text editor to avoid external dependencies). Templates have a "variables" field shown as a comma-separated tag display.

- [ ] **Step 1: Create `app/Filament/Resources/DocumentTemplateResource.php`**

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTemplateResource\Pages;
use App\Models\DocumentTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentTemplateResource extends Resource
{
    protected static ?string $model = DocumentTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Template Details')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(150)
                    ->columnSpanFull(),

                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'receipt'           => 'Receipt',
                        'letterhead'        => 'Letterhead',
                        'contract_employee' => 'Employee Contract',
                        'contract_business' => 'Business Contract',
                    ]),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])->columns(2),

            Forms\Components\Section::make('Template Variables')
                ->description('List the placeholder variable names used in the body (e.g. customer_name, amount). These appear as {{variable_name}} in the body.')
                ->schema([
                    Forms\Components\TagsInput::make('variables')
                        ->label('Variables')
                        ->columnSpanFull()
                        ->helperText('Press Enter after each variable name. Use snake_case (e.g. customer_name)'),
                ]),

            Forms\Components\Section::make('Template Body')
                ->description('HTML body of the document. Use {{variable_name}} placeholders. Plain HTML with inline styles is recommended for PDF compatibility.')
                ->schema([
                    Forms\Components\Textarea::make('body')
                        ->required()
                        ->rows(25)
                        ->columnSpanFull()
                        ->extraAttributes(['style' => 'font-family: monospace; font-size: 0.8125rem;']),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'success' => 'receipt',
                        'info'    => 'letterhead',
                        'warning' => 'contract_employee',
                        'danger'  => 'contract_business',
                    ])
                    ->formatStateUsing(fn ($state) => DocumentTemplate::typeLabel($state)),

                Tables\Columns\TextColumn::make('documents_count')
                    ->counts('documents')
                    ->label('Used')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'receipt'           => 'Receipt',
                        'letterhead'        => 'Letterhead',
                        'contract_employee' => 'Employee Contract',
                        'contract_business' => 'Business Contract',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (DocumentTemplate $record) => $record->documents()->exists()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDocumentTemplates::route('/'),
            'create' => Pages\CreateDocumentTemplate::route('/create'),
            'edit'   => Pages\EditDocumentTemplate::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 2: Create the 3 page classes**

Create `app/Filament/Resources/DocumentTemplateResource/Pages/ListDocumentTemplates.php`:

```php
<?php

namespace App\Filament\Resources\DocumentTemplateResource\Pages;

use App\Filament\Resources\DocumentTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentTemplates extends ListRecords
{
    protected static string $resource = DocumentTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
```

Create `app/Filament/Resources/DocumentTemplateResource/Pages/CreateDocumentTemplate.php`:

```php
<?php

namespace App\Filament\Resources\DocumentTemplateResource\Pages;

use App\Filament\Resources\DocumentTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentTemplate extends CreateRecord
{
    protected static string $resource = DocumentTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

Create `app/Filament/Resources/DocumentTemplateResource/Pages/EditDocumentTemplate.php`:

```php
<?php

namespace App\Filament\Resources\DocumentTemplateResource\Pages;

use App\Filament\Resources\DocumentTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentTemplate extends EditRecord
{
    protected static string $resource = DocumentTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()
            ->hidden(fn () => $this->record->documents()->exists())];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

- [ ] **Step 3: Run full test suite**

```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 46 tests pass.

- [ ] **Step 4: Commit**

```bash
git add app/Filament/Resources/DocumentTemplateResource.php app/Filament/Resources/DocumentTemplateResource/
git commit -m "feat: add Filament DocumentTemplateResource for admin template management"
```

---

## Task 4: Filament DocumentResource (Admin — Issue, View, Track)

**Files:**
- Create: `app/Filament/Resources/DocumentResource.php`
- Create: `app/Filament/Resources/DocumentResource/Pages/ListDocuments.php`
- Create: `app/Filament/Resources/DocumentResource/Pages/CreateDocument.php`
- Create: `app/Filament/Resources/DocumentResource/Pages/ViewDocument.php`

The DocumentResource lets admins issue documents from templates, filling in variable values. It auto-generates the reference number and rendered body at creation time. The view page shows the document status, signing status, and provides a "Download PDF" button and "Preview" iframe.

- [ ] **Step 1: Add test for document generation**

Add to `tests/Feature/DocumentTemplatesTest.php`:

```php
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

        $doc = new Document();
        $rendered = $doc->renderTemplate($template, [
            'customer_name' => 'Dr. Ambe',
            'amount'        => '150,000',
        ]);

        $this->assertStringContainsString('Dr. Ambe', $rendered);
        $this->assertStringContainsString('150,000', $rendered);
        $this->assertStringNotContainsString('{{customer_name}}', $rendered);
    }
```

Run tests to verify PASS:
```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/DocumentTemplatesTest.php
```
Expected: 7 tests pass.

- [ ] **Step 2: Create `app/Filament/Resources/DocumentResource.php`**

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 11;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'support']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Document Setup')->schema([
                Forms\Components\Select::make('document_template_id')
                    ->label('Template')
                    ->options(
                        DocumentTemplate::where('is_active', true)
                            ->get()
                            ->mapWithKeys(fn ($t) => [$t->id => "[{$t->type}] {$t->name}"])
                    )
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        if (!$state) return;
                        $template = DocumentTemplate::find($state);
                        if ($template) {
                            $set('type', $template->type);
                            $set('title', $template->name);
                        }
                    }),

                Forms\Components\Hidden::make('type'),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(200),

                Forms\Components\DatePicker::make('valid_until')
                    ->label('Valid Until')
                    ->nullable(),

                Forms\Components\Toggle::make('requires_signature')
                    ->label('Requires Digital Signature')
                    ->default(false),
            ])->columns(2),

            Forms\Components\Section::make('Recipient')->schema([
                Forms\Components\Select::make('addressee_user_id')
                    ->label('System User (Customer / Employee)')
                    ->options(User::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if (!$state) return;
                        $user = User::find($state);
                        if ($user) {
                            $set('addressee_name', $user->name);
                            $set('addressee_email', $user->email);
                        }
                    }),

                Forms\Components\TextInput::make('addressee_name')
                    ->label('Recipient Name')
                    ->required()
                    ->maxLength(150),

                Forms\Components\TextInput::make('addressee_email')
                    ->label('Recipient Email')
                    ->email()
                    ->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Template Variables')
                ->description('Fill in values for each template placeholder.')
                ->schema([
                    Forms\Components\KeyValue::make('variable_values')
                        ->label('Variable Values')
                        ->columnSpanFull()
                        ->keyLabel('Variable')
                        ->valueLabel('Value')
                        ->reorderable(false)
                        ->helperText('These values will replace {{variable_name}} placeholders in the template body.'),
                ]),

            Forms\Components\Section::make('Notes')->schema([
                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Reference')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'success' => 'receipt',
                        'info'    => 'letterhead',
                        'warning' => 'contract_employee',
                        'danger'  => 'contract_business',
                    ])
                    ->formatStateUsing(fn ($state) => DocumentTemplate::typeLabel($state)),

                Tables\Columns\TextColumn::make('addressee_name')
                    ->label('Recipient')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'primary'   => 'sent',
                        'warning'   => 'pending_signature',
                        'success'   => 'signed',
                        'danger'    => 'voided',
                    ]),

                Tables\Columns\IconColumn::make('requires_signature')
                    ->label('Sig. Required')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Issued')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'receipt'           => 'Receipt',
                        'letterhead'        => 'Letterhead',
                        'contract_employee' => 'Employee Contract',
                        'contract_business' => 'Business Contract',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'             => 'Draft',
                        'sent'              => 'Sent',
                        'pending_signature' => 'Pending Signature',
                        'signed'            => 'Signed',
                        'voided'            => 'Voided',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('download_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Document $record) => route('documents.pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('void')
                    ->label('Void')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->hidden(fn (Document $record) => in_array($record->status, ['signed', 'voided']))
                    ->action(fn (Document $record) => $record->update(['status' => 'voided'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'view'   => Pages\ViewDocument::route('/{record}'),
        ];
    }
}
```

- [ ] **Step 3: Create page classes**

Create `app/Filament/Resources/DocumentResource/Pages/ListDocuments.php`:

```php
<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
```

Create `app/Filament/Resources/DocumentResource/Pages/CreateDocument.php`:

```php
<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use App\Models\Document;
use App\Models\DocumentTemplate;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $template = DocumentTemplate::findOrFail($data['document_template_id']);

        $variables = $data['variable_values'] ?? [];

        $data['type']           = $template->type;
        $data['issued_by']      = auth()->id();
        $data['reference_number'] = Document::generateReferenceNumber($template->type);
        $data['body_rendered']  = (new Document())->renderTemplate($template, $variables);
        $data['status']         = 'draft';

        if (!empty($data['requires_signature'])) {
            $data['signature_token']            = \Illuminate\Support\Str::random(64);
            $data['signature_token_expires_at'] = now()->addDays(30);
            $data['status']                     = 'pending_signature';
        }

        unset($data['variable_values']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
```

Create `app/Filament/Resources/DocumentResource/Pages/ViewDocument.php`:

```php
<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Document Details')->schema([
                Infolists\Components\TextEntry::make('reference_number')->copyable(),
                Infolists\Components\TextEntry::make('title'),
                Infolists\Components\TextEntry::make('type')
                    ->formatStateUsing(fn ($state) => \App\Models\DocumentTemplate::typeLabel($state)),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->color(fn ($state) => match ($state) {
                        'signed'            => 'success',
                        'pending_signature' => 'warning',
                        'voided'            => 'danger',
                        'sent'              => 'primary',
                        default             => 'secondary',
                    }),
                Infolists\Components\TextEntry::make('addressee_name')->label('Recipient'),
                Infolists\Components\TextEntry::make('addressee_email')->label('Recipient Email'),
                Infolists\Components\TextEntry::make('issuer.name')->label('Issued By'),
                Infolists\Components\TextEntry::make('created_at')->label('Issued At')->dateTime('d M Y H:i'),
                Infolists\Components\TextEntry::make('valid_until')->date('d M Y')->placeholder('—'),
                Infolists\Components\IconEntry::make('requires_signature')->boolean(),
            ])->columns(2),

            Infolists\Components\Section::make('Signature Status')
                ->hidden(fn () => !$this->record->requires_signature)
                ->schema([
                    Infolists\Components\TextEntry::make('signature_token')
                        ->label('Signing Link')
                        ->formatStateUsing(fn ($state) => $state ? route('documents.sign', $state) : '—')
                        ->copyable(),
                    Infolists\Components\TextEntry::make('signature_token_expires_at')
                        ->label('Token Expires')
                        ->dateTime('d M Y H:i'),
                    Infolists\Components\TextEntry::make('signed_by_name')->placeholder('Not signed yet'),
                    Infolists\Components\TextEntry::make('signed_at')->dateTime('d M Y H:i')->placeholder('—'),
                    Infolists\Components\TextEntry::make('signed_ip')->label('Signed From IP')->placeholder('—'),
                ])->columns(2),

            Infolists\Components\Section::make('Document Preview')->schema([
                Infolists\Components\ViewEntry::make('body_rendered')
                    ->view('filament.infolists.document-preview')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn () => route('documents.pdf', $this->record))
                ->openUrlInNewTab(),
            Actions\Action::make('mark_sent')
                ->label('Mark as Sent')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->hidden(fn () => $this->record->status !== 'draft')
                ->action(fn () => $this->record->update(['status' => 'sent']))
                ->after(fn () => $this->refreshFormData(['status'])),
            Actions\Action::make('void')
                ->label('Void Document')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(fn () => in_array($this->record->status, ['signed', 'voided']))
                ->action(fn () => $this->record->update(['status' => 'voided']))
                ->after(fn () => $this->refreshFormData(['status'])),
        ];
    }
}
```

- [ ] **Step 4: Create Filament infolist view for document preview**

Create `resources/views/filament/infolists/document-preview.blade.php`:

```html
<div style="border: 1px solid #334155; border-radius: 8px; overflow: hidden; background: white;">
    <div style="background: #1e293b; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center;">
        <span style="color: #94a3b8; font-size: 0.8125rem;">Document Preview</span>
        <a href="{{ route('documents.pdf', $getRecord()) }}"
           target="_blank"
           style="color: #00C896; font-size: 0.75rem; text-decoration: none;">
            Download PDF ↗
        </a>
    </div>
    <div style="padding: 1.5rem; overflow-x: auto; max-height: 600px; overflow-y: auto;">
        {!! $getRecord()->body_rendered !!}
    </div>
</div>
```

- [ ] **Step 5: Add PDF download and document routes to `routes/web.php`**

Add to `routes/web.php` (before the locale-prefixed group, after the auth routes):

```php
// Document routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/documents/{document}/pdf',  [\App\Http\Controllers\DocumentController::class, 'pdf'])->name('documents.pdf');
    Route::get('/documents/{document}/view', [\App\Http\Controllers\DocumentController::class, 'preview'])->name('documents.preview');
});

// Public document signing (no auth — token-based)
Route::get('/documents/{token}/sign',  [\App\Http\Controllers\DocumentSigningController::class, 'show'])->name('documents.sign');
Route::post('/documents/{token}/sign', [\App\Http\Controllers\DocumentSigningController::class, 'sign'])->name('documents.sign.submit');
```

**Note:** The `DocumentController` (for PDF download) is different from `DocumentSigningController`. Name the PDF controller `app/Http/Controllers/DocumentController.php`.

- [ ] **Step 6: Create `app/Http/Controllers/DocumentController.php`**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function pdf(Request $request, Document $document)
    {
        // Only the addressee user or admin/super_admin can download
        $user = $request->user();
        $canAccess = $user->hasAnyRole(['super_admin', 'admin', 'support'])
            || ($document->addressee_user_id && $document->addressee_user_id === $user->id);

        abort_unless($canAccess, 403);

        $pdf = Pdf::loadView('documents.pdf', compact('document'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($document->reference_number . '.pdf');
    }

    public function preview(Request $request, Document $document)
    {
        $user = $request->user();
        $canAccess = $user->hasAnyRole(['super_admin', 'admin', 'support'])
            || ($document->addressee_user_id && $document->addressee_user_id === $user->id);

        abort_unless($canAccess, 403);

        return view('documents.pdf', compact('document'));
    }
}
```

- [ ] **Step 7: Run full test suite**

```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 48 tests pass (46 + 2 new from step 1).

- [ ] **Step 8: Commit**

```bash
git add app/Filament/Resources/DocumentResource.php app/Filament/Resources/DocumentResource/ app/Http/Controllers/DocumentController.php resources/views/filament/ routes/web.php
git commit -m "feat: add Filament DocumentResource with create, view, PDF download, and void actions"
```

---

## Task 5: Digital Signing Page

**Files:**
- Create: `app/Http/Controllers/DocumentSigningController.php`
- Create: `resources/views/documents/sign.blade.php`

The signing page is public (no auth). It is reached via a unique 64-char token in the URL (`/documents/{token}/sign`). The page shows the document content and a signing form with:
- Typed name field (required)
- Optional canvas drawing for drawn signature
- Submit button that records the signature

After signing, the document `status` is updated to `signed`, `signed_at`, `signed_by_name`, `signed_ip`, and `signed_data` (typed_name + optional canvas data URL) are stored. The token is nullified to prevent re-signing.

- [ ] **Step 1: Add signing tests**

Add to `tests/Feature/DocumentTemplatesTest.php`:

```php
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
```

- [ ] **Step 2: Run the new tests — expect FAIL (controller/route don't exist yet)**

```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/DocumentTemplatesTest.php --filter=test_signing_page_loads_with_valid_token
```

Expected: FAIL

- [ ] **Step 3: Create `app/Http/Controllers/DocumentSigningController.php`**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentSigningController extends Controller
{
    public function show(string $token)
    {
        $document = Document::where('signature_token', $token)->firstOrFail();

        if (!$document->isSigningTokenValid()) {
            return view('documents.sign', ['document' => $document, 'expired' => true]);
        }

        return view('documents.sign', ['document' => $document, 'expired' => false]);
    }

    public function sign(Request $request, string $token)
    {
        $document = Document::where('signature_token', $token)->firstOrFail();

        abort_unless($document->isSigningTokenValid(), 410, 'This signing link has expired or is no longer valid.');

        $validated = $request->validate([
            'typed_name'     => 'required|string|max:150',
            'canvas_data'    => 'nullable|string|max:100000',
        ]);

        $document->update([
            'status'          => 'signed',
            'signed_at'       => now(),
            'signed_by_name'  => $validated['typed_name'],
            'signed_ip'       => $request->ip(),
            'signed_data'     => [
                'typed_name'  => $validated['typed_name'],
                'canvas_data' => $validated['canvas_data'] ?? null,
            ],
            'signature_token'            => null,
            'signature_token_expires_at' => null,
        ]);

        return redirect()->route('documents.sign.success', $document->reference_number);
    }
}
```

- [ ] **Step 4: Add success route to `routes/web.php`**

Add after the existing document signing routes:

```php
Route::get('/documents/signed/{reference}', function ($reference) {
    return view('documents.sign-success', ['reference' => $reference]);
})->name('documents.sign.success');
```

- [ ] **Step 5: Create `resources/views/documents/sign.blade.php`**

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Document — OPES Health Systems</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body" style="align-items:flex-start; padding-top:2rem;">
    <div style="width:100%;max-width:720px;margin:0 auto;padding:0 1rem 4rem;">
        <div style="text-align:center;margin-bottom:2rem;">
            <span class="auth-brand-opes">OPES</span>
            <span class="auth-brand-name"> Health Systems</span>
        </div>

        @if($expired)
            <div class="auth-card" style="text-align:center;padding:3rem;">
                <div style="font-size:3rem;margin-bottom:1rem;">⚠️</div>
                <h1 style="color:#f1f5f9;font-size:1.25rem;font-weight:700;">Signing Link Expired</h1>
                <p style="color:#64748b;margin-top:0.5rem;">This document signing link has expired or has already been used.</p>
                <p style="color:#64748b;font-size:0.8125rem;margin-top:1rem;">If you need to sign this document, please contact <a href="mailto:support@opeshealthsystems.com" class="auth-link">support@opeshealthsystems.com</a>.</p>
            </div>
        @elseif($document->isSigned())
            <div class="auth-card" style="text-align:center;padding:3rem;">
                <div style="font-size:3rem;margin-bottom:1rem;color:#00C896;">✓</div>
                <h1 style="color:#f1f5f9;font-size:1.25rem;font-weight:700;">Document Already Signed</h1>
                <p style="color:#64748b;margin-top:0.5rem;">Signed by <strong style="color:#e2e8f0;">{{ $document->signed_by_name }}</strong> on {{ $document->signed_at?->format('d M Y') }}.</p>
            </div>
        @else
            <div class="auth-card">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
                    <div>
                        <h1 class="auth-heading">{{ $document->title }}</h1>
                        <p class="auth-subheading">Ref: {{ $document->reference_number }} | Addressee: {{ $document->addressee_name }}</p>
                    </div>
                    <span style="background:rgba(234,179,8,0.15);color:#eab308;font-size:0.75rem;font-weight:600;padding:0.3rem 0.75rem;border-radius:20px;text-transform:uppercase;letter-spacing:0.05em;">
                        Awaiting Signature
                    </span>
                </div>

                <div style="background:#0F172A;border:1px solid #334155;border-radius:8px;padding:1.5rem;max-height:400px;overflow-y:auto;margin-bottom:1.5rem;">
                    {!! $document->body_rendered !!}
                </div>

                @if ($errors->any())
                    <div class="auth-error-box" style="margin-bottom:1rem;">
                        @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('documents.sign.submit', $document->signature_token) }}" id="sign-form">
                    @csrf

                    <div class="auth-field" style="margin-bottom:1rem;">
                        <label for="typed_name" class="auth-label">Type your full name to sign *</label>
                        <input id="typed_name" name="typed_name" type="text"
                            class="auth-input @error('typed_name') auth-input-error @enderror"
                            value="{{ old('typed_name', $document->addressee_name) }}"
                            required placeholder="Type your full legal name"
                            autocomplete="name">
                    </div>

                    <div class="auth-field" style="margin-bottom:1.5rem;">
                        <label class="auth-label">Optional: Draw your signature</label>
                        <div style="position:relative;">
                            <canvas id="sig-canvas" width="640" height="140"
                                style="border:1px solid #334155;border-radius:8px;background:#fff;width:100%;cursor:crosshair;touch-action:none;">
                            </canvas>
                            <button type="button" id="clear-canvas"
                                style="position:absolute;top:0.5rem;right:0.5rem;background:rgba(15,23,42,0.8);color:#94a3b8;border:1px solid #334155;border-radius:4px;padding:0.25rem 0.5rem;font-size:0.75rem;cursor:pointer;">
                                Clear
                            </button>
                        </div>
                        <input type="hidden" name="canvas_data" id="canvas_data">
                        <p style="color:#475569;font-size:0.75rem;margin-top:0.375rem;">Drawing is optional — your typed name above is the binding signature.</p>
                    </div>

                    <div style="background:rgba(0,200,150,0.05);border:1px solid rgba(0,200,150,0.15);border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
                        <p style="color:#94a3b8;font-size:0.8125rem;line-height:1.6;">
                            By clicking "Sign Document" below, you agree that your typed name constitutes a legal digital signature on this document, and that you have read and understood its contents. Your signature will be timestamped and your IP address recorded.
                        </p>
                    </div>

                    <button type="submit" class="auth-btn" id="sign-btn">Sign Document</button>
                </form>
            </div>
        @endif

        <p class="auth-footer-note" style="margin-top:1.5rem;text-align:center;">
            &copy; {{ date('Y') }} OPES Health Systems SARL — Douala, Cameroon
        </p>
    </div>

    <script>
    (function() {
        const canvas = document.getElementById('sig-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let drawing = false, hasDrawing = false;

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            const src = e.touches ? e.touches[0] : e;
            return {
                x: (src.clientX - rect.left) * scaleX,
                y: (src.clientY - rect.top) * scaleY,
            };
        }

        ctx.strokeStyle = '#0f172a';
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        canvas.addEventListener('mousedown',  e => { drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        canvas.addEventListener('mousemove',  e => { if (!drawing) return; hasDrawing = true; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
        canvas.addEventListener('mouseup',    () => { drawing = false; });
        canvas.addEventListener('mouseleave', () => { drawing = false; });
        canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        canvas.addEventListener('touchmove',  e => { e.preventDefault(); if (!drawing) return; hasDrawing = true; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
        canvas.addEventListener('touchend',   () => { drawing = false; });

        document.getElementById('clear-canvas').addEventListener('click', function () {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasDrawing = false;
            document.getElementById('canvas_data').value = '';
        });

        document.getElementById('sign-form').addEventListener('submit', function () {
            if (hasDrawing) {
                document.getElementById('canvas_data').value = canvas.toDataURL('image/png');
            }
        });
    })();
    </script>
</body>
</html>
```

- [ ] **Step 6: Create `resources/views/documents/sign-success.blade.php`**

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signed — OPES Health Systems</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div style="text-align:center;margin-bottom:1rem;">
            <span class="auth-brand-opes">OPES</span>
            <span class="auth-brand-name"> Health Systems</span>
        </div>
        <div class="auth-card" style="text-align:center;padding:3rem;">
            <div style="font-size:3.5rem;color:#00C896;margin-bottom:1rem;">✓</div>
            <h1 style="color:#f1f5f9;font-size:1.5rem;font-weight:700;">Document Signed Successfully</h1>
            <p style="color:#94a3b8;margin-top:0.75rem;">Reference: <strong style="color:#e2e8f0;">{{ $reference }}</strong></p>
            <p style="color:#64748b;font-size:0.875rem;margin-top:1rem;line-height:1.6;">
                Your digital signature has been recorded. You will receive a confirmation copy via email shortly.
                If you have questions, contact <a href="mailto:support@opeshealthsystems.com" class="auth-link">support@opeshealthsystems.com</a>.
            </p>
        </div>
        <p class="auth-footer-note">&copy; {{ date('Y') }} OPES Health Systems SARL</p>
    </div>
</body>
</html>
```

- [ ] **Step 7: Run all new signing tests**

```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/DocumentTemplatesTest.php
```

Expected: All 11 tests pass.

- [ ] **Step 8: Run full suite**

```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 48 + 3 = 51 tests pass.

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/DocumentSigningController.php resources/views/documents/ routes/web.php
git commit -m "feat: add digital signing workflow with token-based public signing page and canvas signature capture"
```

---

## Task 6: Customer Portal — My Documents Section

**Files:**
- Create: `app/Http/Controllers/Customer/DocumentController.php`
- Create: `resources/views/customer/documents/index.blade.php`
- Create: `resources/views/customer/documents/show.blade.php`
- Modify: `routes/web.php` — add `/{locale}/customer/documents` routes
- Modify: `resources/views/components/layouts/customer.blade.php` — add Documents nav link

- [ ] **Step 1: Add customer portal document tests**

Add to `tests/Feature/DocumentTemplatesTest.php`:

```php
    public function test_customer_can_see_their_documents(): void
    {
        $admin    = User::factory()->create();
        $admin->assignRole('admin');
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $template = DocumentTemplate::create([
            'name' => 'T', 'type' => 'receipt',
            'body' => '<p>Receipt</p>', 'variables' => [], 'is_active' => true,
        ]);

        Document::create([
            'document_template_id' => $template->id,
            'type'                 => 'receipt',
            'title'                => 'My Receipt',
            'reference_number'     => 'RCT-2026-00042',
            'body_rendered'        => '<p>Receipt</p>',
            'issued_by'            => $admin->id,
            'addressee_user_id'    => $customer->id,
            'addressee_name'       => $customer->name,
            'status'               => 'sent',
            'requires_signature'   => false,
        ]);

        $this->actingAs($customer)
            ->get('/en/customer/documents')
            ->assertOk()
            ->assertSee('My Receipt');
    }

    public function test_customer_cannot_see_another_customers_document(): void
    {
        $admin     = User::factory()->create();
        $admin->assignRole('admin');
        $customer1 = User::factory()->create();
        $customer1->assignRole('customer');
        $customer2 = User::factory()->create();
        $customer2->assignRole('customer');

        $template = DocumentTemplate::create([
            'name' => 'T2', 'type' => 'receipt',
            'body' => '<p>R</p>', 'variables' => [], 'is_active' => true,
        ]);

        $doc = Document::create([
            'document_template_id' => $template->id,
            'type'                 => 'receipt',
            'title'                => 'Private Receipt',
            'reference_number'     => 'RCT-2026-00043',
            'body_rendered'        => '<p>R</p>',
            'issued_by'            => $admin->id,
            'addressee_user_id'    => $customer1->id,
            'addressee_name'       => $customer1->name,
            'status'               => 'sent',
            'requires_signature'   => false,
        ]);

        $this->actingAs($customer2)
            ->get('/en/customer/documents/' . $doc->id)
            ->assertForbidden();
    }
```

- [ ] **Step 2: Add customer document routes to `routes/web.php`**

Inside the locale-prefixed group, in the customer portal section, add:

```php
Route::get('/documents',      [\App\Http\Controllers\Customer\DocumentController::class, 'index'])->name('documents');
Route::get('/documents/{id}', [\App\Http\Controllers\Customer\DocumentController::class, 'show'])->name('documents.show');
```

These go inside `Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')`.

- [ ] **Step 3: Create `app/Http/Controllers/Customer/DocumentController.php`**

```php
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $documents = Document::where('addressee_user_id', $user->id)
            ->whereNotIn('status', ['draft'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('customer.documents.index', compact('documents'));
    }

    public function show(int $id)
    {
        $user     = Auth::user();
        $document = Document::where('id', $id)
            ->where('addressee_user_id', $user->id)
            ->firstOrFail();

        abort_if(in_array($document->status, ['draft']), 403);

        return view('customer.documents.show', compact('document'));
    }
}
```

- [ ] **Step 4: Create `resources/views/customer/documents/index.blade.php`**

```html
<x-layouts.customer title="My Documents">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">My Documents</h1>
            <p class="cp-page-subtitle">Receipts, contracts, and official correspondence issued to you</p>
        </div>
    </div>

    @if($documents->isEmpty())
        <div class="cp-section-card" style="text-align:center;padding:3rem;">
            <div class="cp-empty-state">
                <i data-lucide="file-text" style="width:48px;height:48px;color:#334155"></i>
                <p>No documents issued yet.</p>
                <p style="font-size:0.8125rem">Documents from OPES Health Systems will appear here once issued.</p>
            </div>
        </div>
    @else
        <div class="cp-section-card">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #334155;">
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Reference</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Title</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Type</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Date</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                    <tr style="border-bottom:1px solid #1e293b;">
                        <td style="padding:0.75rem;color:#00C896;font-size:0.8125rem;font-family:monospace;">{{ $doc->reference_number }}</td>
                        <td style="padding:0.75rem;color:#e2e8f0;font-size:0.875rem;">{{ $doc->title }}</td>
                        <td style="padding:0.75rem;">
                            <span style="background:rgba(100,116,139,0.15);color:#94a3b8;font-size:0.7rem;font-weight:600;padding:0.2rem 0.5rem;border-radius:20px;text-transform:uppercase;letter-spacing:0.04em;">
                                {{ \App\Models\DocumentTemplate::typeLabel($doc->type) }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;">
                            @php
                                $statusColor = match($doc->status) {
                                    'signed' => '#00C896',
                                    'pending_signature' => '#eab308',
                                    'voided' => '#ef4444',
                                    default => '#94a3b8',
                                };
                            @endphp
                            <span style="color:{{ $statusColor }};font-size:0.8125rem;font-weight:600;text-transform:capitalize;">
                                {{ str_replace('_', ' ', $doc->status) }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;color:#64748b;font-size:0.8125rem;">{{ $doc->created_at->format('d M Y') }}</td>
                        <td style="padding:0.75rem;text-align:right;">
                            <a href="{{ route('customer.documents.show', ['locale' => app()->getLocale(), 'id' => $doc->id]) }}"
                               class="cp-btn-outline" style="font-size:0.75rem;padding:0.375rem 0.75rem;">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:1rem 0.75rem 0;">
                {{ $documents->links() }}
            </div>
        </div>
    @endif
</x-layouts.customer>
```

- [ ] **Step 5: Create `resources/views/customer/documents/show.blade.php`**

```html
<x-layouts.customer title="{{ $document->title }}">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">{{ $document->title }}</h1>
            <p class="cp-page-subtitle">Ref: {{ $document->reference_number }} · {{ \App\Models\DocumentTemplate::typeLabel($document->type) }}</p>
        </div>
        <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
            @if($document->status === 'pending_signature' && $document->signature_token)
                <a href="{{ route('documents.sign', $document->signature_token) }}" class="cp-btn-primary">
                    <i data-lucide="pen-line" style="width:15px;height:15px"></i> Sign Document
                </a>
            @endif
            <a href="{{ route('documents.pdf', $document) }}" class="cp-btn-outline">
                <i data-lucide="download" style="width:15px;height:15px"></i> Download PDF
            </a>
            <a href="{{ route('customer.documents', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">
                ← Back
            </a>
        </div>
    </div>

    @if($document->isSigned())
        <div style="background:rgba(0,200,150,0.08);border:1px solid rgba(0,200,150,0.25);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;">
            <span style="color:#00C896;font-size:1.5rem;font-weight:700;">✓</span>
            <div>
                <p style="color:#00C896;font-weight:600;font-size:0.9rem;margin:0;">Signed by {{ $document->signed_by_name }}</p>
                <p style="color:#64748b;font-size:0.8rem;margin:0.1rem 0 0;">{{ $document->signed_at?->format('d M Y, H:i') }} UTC</p>
            </div>
        </div>
    @elseif($document->status === 'pending_signature')
        <div style="background:rgba(234,179,8,0.08);border:1px solid rgba(234,179,8,0.25);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;">
            <span style="color:#eab308;font-size:1.5rem;">⏳</span>
            <div>
                <p style="color:#eab308;font-weight:600;font-size:0.9rem;margin:0;">Awaiting your signature</p>
                <p style="color:#64748b;font-size:0.8rem;margin:0.1rem 0 0;">
                    Token expires {{ $document->signature_token_expires_at?->format('d M Y') }}
                </p>
            </div>
        </div>
    @endif

    <div class="cp-section-card" style="padding:0;">
        <div style="background:#1e293b;padding:0.75rem 1.25rem;border-bottom:1px solid #334155;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center;">
            <span style="color:#94a3b8;font-size:0.8125rem;">Document Content</span>
            <span style="color:#64748b;font-size:0.75rem;">Issued {{ $document->created_at->format('d M Y') }}</span>
        </div>
        <div style="padding:2rem;background:white;border-radius:0 0 12px 12px;overflow-x:auto;">
            {!! $document->body_rendered !!}
        </div>
    </div>
</x-layouts.customer>
```

- [ ] **Step 6: Add Documents link to `resources/views/components/layouts/customer.blade.php`**

In the nav links section, add a Documents link after the Dashboard link:

```html
<a href="{{ route('customer.documents', ['locale' => app()->getLocale()]) }}"
   class="cp-nav-link {{ request()->routeIs('customer.documents*') ? 'cp-nav-link-active' : '' }}">
    <i data-lucide="file-text" style="width:16px;height:16px"></i> Documents
</a>
```

- [ ] **Step 7: Run all tests**

```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Expected: 51 + 2 = 53 tests pass.

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/Customer/DocumentController.php resources/views/customer/documents/ resources/views/components/layouts/customer.blade.php routes/web.php tests/Feature/DocumentTemplatesTest.php
git commit -m "feat: add customer portal My Documents section with list, view, and sign CTA"
```

---

## Self-Review

### 1. Spec coverage

| Requirement | Covered |
|---|---|
| Receipts template | ✅ Task 1 seeder + DocumentTemplate type=receipt |
| Letterhead template | ✅ Task 1 seeder + type=letterhead |
| Employee contract template | ✅ Task 1 seeder + type=contract_employee |
| Business contract template | ✅ Task 1 seeder + type=contract_business |
| Admin creates documents from templates | ✅ Task 3 DocumentTemplateResource, Task 4 DocumentResource |
| Variable substitution | ✅ Document::renderTemplate() in Task 1 |
| Auto reference numbers | ✅ Document::generateReferenceNumber() in Task 1 |
| PDF download | ✅ Task 2 DomPDF + Task 4 DocumentController@pdf |
| Browser view | ✅ Task 4 DocumentController@preview + customer show view |
| Digital signing with token | ✅ Task 5 DocumentSigningController, sign.blade.php |
| Canvas signature capture | ✅ Task 5 JS canvas in sign.blade.php |
| Typed name as legal signature | ✅ Task 5 signed_by_name field |
| Customer can see their documents | ✅ Task 6 Customer\DocumentController, customer portal views |
| Customer cannot see others' documents | ✅ Task 6 where('addressee_user_id') scope |
| `manage_documents` permission | ✅ Task 1 RolePermissionSeeder |
| Admin can void documents | ✅ Task 4 void action |

### 2. Placeholder scan

None — all steps contain actual code.

### 3. Type consistency

- `Document::template()` uses `document_template_id` FK — matches migration column name.
- `Document::issuer()` uses `issued_by` FK — matches migration column name.
- Route names used in views: `documents.pdf`, `documents.sign`, `documents.sign.submit`, `documents.sign.success`, `customer.documents`, `customer.documents.show` — all registered in routes/web.php.
- `Document::generateReferenceNumber()` uses `DocumentTemplate::referencePrefix()` — consistent naming.
