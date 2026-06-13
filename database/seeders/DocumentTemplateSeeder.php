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
        return '<div class="doc-page"><div class="doc-header"><div class="doc-logo-block"><h1 class="doc-company">OPES Health Systems SARL</h1><p class="doc-company-sub">Digital Healthcare Solutions | Douala, Cameroon</p><p class="doc-company-sub">Email: billing@opeshealthsystems.com | Tel: +237 600 000 000</p></div><div class="doc-stamp"><div class="doc-stamp-label">RECEIPT</div><div class="doc-stamp-ref">{{reference_number}}</div></div></div><div class="doc-divider"></div><div class="doc-meta-row"><div><p class="doc-meta-label">BILLED TO</p><p class="doc-meta-value">{{customer_name}}</p><p class="doc-meta-value">{{customer_email}}</p></div><div><p class="doc-meta-label">PAYMENT DATE</p><p class="doc-meta-value">{{payment_date}}</p></div></div><table class="doc-table"><thead><tr><th>Description</th><th class="text-right">Amount</th></tr></thead><tbody><tr><td>{{item_description}}</td><td class="text-right">{{currency}} {{amount}}</td></tr></tbody><tfoot><tr class="doc-total-row"><td>TOTAL PAID</td><td class="text-right">{{currency}} {{amount}}</td></tr></tfoot></table><div class="doc-footer-note"><p>Thank you for your payment. This receipt serves as confirmation of your transaction with OPES Health Systems SARL.</p></div></div>';
    }

    private function letterheadBody(): string
    {
        return '<div class="doc-page"><div class="doc-header"><div class="doc-logo-block"><h1 class="doc-company">OPES Health Systems SARL</h1><p class="doc-company-sub">Digital Healthcare Solutions | Douala, Cameroon</p></div></div><div class="doc-divider"></div><div style="margin-top:2rem"><p class="doc-date">{{date}}</p><p class="doc-recipient">{{recipient_name}}</p><p class="doc-recipient-addr">{{recipient_address}}</p><p class="doc-subject"><strong>RE: {{subject}}</strong></p><div class="doc-body-text">{{body}}</div></div><div style="margin-top:3rem"><p>Yours sincerely,</p><div class="doc-sig-line" style="margin:2rem 0 0.5rem"></div><p><strong>{{sender_name}}</strong></p><p class="doc-meta-value">{{sender_position}}</p><p class="doc-meta-value">OPES Health Systems SARL</p></div></div>';
    }

    private function employeeContractBody(): string
    {
        return '<div class="doc-page"><div class="doc-header"><div class="doc-logo-block"><h1 class="doc-company">OPES Health Systems SARL</h1></div><div class="doc-stamp"><div class="doc-stamp-label">EMPLOYMENT CONTRACT</div></div></div><div class="doc-divider"></div><h2 class="doc-section-title">CONTRACT OF EMPLOYMENT</h2><p class="doc-body-text">This Employment Contract is entered into on <strong>{{contract_date}}</strong> between OPES Health Systems SARL and <strong>{{employee_name}}</strong> (Employee ID: {{employee_id}}).</p><h3 class="doc-clause-title">1. Position and Duties</h3><p class="doc-body-text">The Employee is appointed to the position of <strong>{{position}}</strong> in the <strong>{{department}}</strong> department.</p><h3 class="doc-clause-title">2. Commencement Date</h3><p class="doc-body-text">Employment shall commence on <strong>{{start_date}}</strong>.</p><h3 class="doc-clause-title">3. Place of Work</h3><p class="doc-body-text">The place of work shall be <strong>{{location}}</strong>.</p><h3 class="doc-clause-title">4. Remuneration</h3><p class="doc-body-text">The Employee shall receive a monthly salary of <strong>{{currency}} {{salary}}</strong>.</p><h3 class="doc-clause-title">5. Governing Law</h3><p class="doc-body-text">This contract shall be governed by the Labour Code of Cameroon.</p></div>';
    }

    private function businessContractBody(): string
    {
        return '<div class="doc-page"><div class="doc-header"><div class="doc-logo-block"><h1 class="doc-company">OPES Health Systems SARL</h1></div><div class="doc-stamp"><div class="doc-stamp-label">SOFTWARE LICENSE AGREEMENT</div></div></div><div class="doc-divider"></div><h2 class="doc-section-title">SOFTWARE LICENSE AND SERVICE AGREEMENT</h2><p class="doc-body-text">This Agreement is entered into on <strong>{{contract_date}}</strong> between OPES Health Systems SARL and <strong>{{business_name}}</strong>, located at {{business_address}}, represented by <strong>{{contact_name}}</strong>.</p><h3 class="doc-clause-title">1. Grant of License</h3><p class="doc-body-text">OPES grants the Client a non-exclusive license to use <strong>{{software_name}}</strong> (Type: <strong>{{license_type}}</strong>) for up to <strong>{{seats}}</strong> users.</p><h3 class="doc-clause-title">2. License Period</h3><p class="doc-body-text">Valid from <strong>{{start_date}}</strong> to <strong>{{end_date}}</strong>.</p><h3 class="doc-clause-title">3. Fees</h3><p class="doc-body-text">The Client shall pay <strong>{{currency}} {{amount}}</strong> for the license period.</p><h3 class="doc-clause-title">4. Governing Law</h3><p class="doc-body-text">This Agreement shall be governed by the laws of Cameroon.</p></div>';
    }
}
