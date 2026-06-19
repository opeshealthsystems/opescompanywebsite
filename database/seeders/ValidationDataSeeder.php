<?php

namespace Database\Seeders;

use App\Models\ValidationModule;
use App\Models\ValidationProduct;
use App\Models\ValidationTestCase;
use App\Models\ValidationWorkflow;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ValidationDataSeeder extends Seeder
{
    public function run(): void
    {
        $product = ValidationProduct::firstOrCreate(
            ['code' => 'ohos'],
            [
                'name' => 'OPES Health OS',
                'description' => 'OPES Health operating system under clinical validation.',
                'is_active' => true,
            ]
        );

        $map = [
            ['name' => 'Patient Registration', 'code' => 'patient_registration', 'workflows' => ['create_new_patient', 'search_existing_patient', 'detect_duplicate_patient', 'generate_health_id', 'print_or_display_qr_code']],
            ['name' => 'Clinical Consultation', 'code' => 'clinical_consultation', 'workflows' => ['capture_vitals', 'record_history', 'document_diagnosis', 'create_treatment_plan', 'prescribe_medication', 'order_laboratory_test', 'complete_visit_note']],
            ['name' => 'Triage', 'code' => 'triage', 'workflows' => ['capture_emergency_symptoms', 'assign_priority_level', 'escalate_critical_patient', 'send_to_consultation_queue']],
            ['name' => 'Laboratory', 'code' => 'laboratory', 'workflows' => ['receive_lab_order', 'collect_sample', 'track_sample', 'enter_result', 'approve_result', 'send_result_to_doctor']],
            ['name' => 'Pharmacy', 'code' => 'pharmacy', 'workflows' => ['receive_prescription', 'check_stock', 'dispense_medication', 'deduct_inventory', 'flag_expired_drug', 'record_substitution']],
            ['name' => 'Billing', 'code' => 'billing', 'workflows' => ['bill_consultation', 'bill_lab_test', 'bill_medication', 'generate_invoice', 'record_payment', 'issue_receipt']],
            ['name' => 'CDMS', 'code' => 'cdms', 'workflows' => ['upload_document', 'classify_document', 'approve_document', 'search_document', 'retrieve_patient_file', 'audit_document_access']],
            ['name' => 'Health ID & Interoperability', 'code' => 'health_id_interoperability', 'workflows' => ['create_health_id', 'match_patient_across_facilities', 'send_referral', 'receive_referral', 'exchange_lab_result', 'view_longitudinal_record']],
            ['name' => 'CDSS', 'code' => 'cdss', 'workflows' => ['trigger_drug_interaction_alert', 'trigger_allergy_alert', 'review_clinical_recommendation', 'accept_or_override_alert', 'document_override_reason']],
            ['name' => 'Reporting', 'code' => 'reporting', 'workflows' => ['generate_daily_report', 'generate_clinical_report', 'generate_financial_report', 'generate_dashboard', 'export_report']],
        ];

        foreach ($map as $moduleData) {
            $module = ValidationModule::firstOrCreate(
                ['validation_product_id' => $product->id, 'code' => $moduleData['code']],
                ['name' => $moduleData['name'], 'is_active' => true]
            );

            foreach ($moduleData['workflows'] as $workflowCode) {
                $humanName = Str::headline($workflowCode);

                $workflow = ValidationWorkflow::firstOrCreate(
                    ['validation_module_id' => $module->id, 'code' => $workflowCode],
                    ['name' => $humanName, 'is_active' => true]
                );

                ValidationTestCase::firstOrCreate(
                    ['validation_workflow_id' => $workflow->id, 'title' => $humanName],
                    ['is_active' => true]
                );
            }
        }
    }
}
