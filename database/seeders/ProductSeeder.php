<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // ── CORE PLATFORM ───────────────────────────────────────
            ['slug' => 'opescare',          'name' => 'OPESCare',          'subtitle' => 'Health ID · Interoperability Layer',                 'category' => 'core',        'icon' => 'fingerprint',   'color' => '#00C896', 'is_featured' => true,  'sort_order' => 1,  'tagline' => 'One universal Health ID for every patient — connecting all 22 OPES systems.'],
            ['slug' => 'opes-emr',          'name' => 'OPES EMR',          'subtitle' => 'Electronic Medical Records · Clinics & Hospitals',   'category' => 'core',        'icon' => 'stethoscope',   'color' => '#00C896', 'is_featured' => true,  'sort_order' => 2,  'tagline' => 'Full-featured EMR for clinics and small hospitals, bilingual EN/FR.'],
            ['slug' => 'opes-hospital-his', 'name' => 'OPES Hospital HIS', 'subtitle' => 'Full Hospital Information System',                   'category' => 'core',        'icon' => 'hospital',      'color' => '#00C896', 'is_featured' => true,  'sort_order' => 3,  'tagline' => 'End-to-end hospital management — inpatient, outpatient, billing and more.'],
            ['slug' => 'uhc-is',            'name' => 'UHC IS',            'subtitle' => 'Universal Health Coverage Information System',        'category' => 'core',        'icon' => 'users',         'color' => '#00C896', 'is_featured' => false, 'sort_order' => 4,  'tagline' => 'Track and manage UHC enrolment, benefits, and eligibility nationally.'],
            ['slug' => 'opes-triage',       'name' => 'Opes Triage',       'subtitle' => 'Standalone Triage Management · Any Facility',         'category' => 'core',        'icon' => 'timer',         'color' => '#00C896', 'is_featured' => true,  'sort_order' => 5,  'tagline' => 'Reduce patient wait times with smart triage — works with any existing system.'],
            // ── DIAGNOSTICS ─────────────────────────────────────────
            ['slug' => 'opes-lab',          'name' => 'OPES Lab',          'subtitle' => 'Laboratory Information System',                       'category' => 'diagnostics', 'icon' => 'microscope',    'color' => '#1A6FE8', 'is_featured' => false, 'sort_order' => 10, 'tagline' => 'Manage lab orders, results, and sample tracking with HL7 FHIR integration.'],
            ['slug' => 'pharmis',           'name' => 'PHARMIS',           'subtitle' => 'Pharmacy Information System',                         'category' => 'diagnostics', 'icon' => 'pill',          'color' => '#1A6FE8', 'is_featured' => false, 'sort_order' => 11, 'tagline' => 'Full pharmacy management: dispensing, inventory, and prescriptions.'],
            ['slug' => 'radis',             'name' => 'RADIS',             'subtitle' => 'Radiology Information System',                        'category' => 'diagnostics', 'icon' => 'image-up',      'color' => '#1A6FE8', 'is_featured' => false, 'sort_order' => 12, 'tagline' => 'Schedule, capture, and report radiology studies with PACS integration.'],
            // ── SPECIALIST SYSTEMS ──────────────────────────────────
            ['slug' => 'cardis',            'name' => 'CARDIS',            'subtitle' => 'Cardiology Information System',                       'category' => 'specialist',  'icon' => 'heart-pulse',   'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 20, 'tagline' => 'Cardiology-specific workflows: ECGs, echo reports, and cardiac care pathways.'],
            ['slug' => 'dentis',            'name' => 'DENTIS',            'subtitle' => 'Dental Information System',                           'category' => 'specialist',  'icon' => 'scan-face',     'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 21, 'tagline' => 'Dental charting, treatment plans, and patient records for dental practices.'],
            ['slug' => 'dermis',            'name' => 'DERMIS',            'subtitle' => 'Dermatology Information System',                      'category' => 'specialist',  'icon' => 'layers',        'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 22, 'tagline' => 'Skin condition documentation, photo records, and dermatology care plans.'],
            ['slug' => 'endois',            'name' => 'ENDOIS',            'subtitle' => 'Endocrinology & Diabetes Information System',         'category' => 'specialist',  'icon' => 'gauge',         'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 23, 'tagline' => 'Diabetes and endocrine disorder management with glycaemic tracking.'],
            ['slug' => 'gynobsis',          'name' => 'GYNOBSIS',          'subtitle' => 'Obstetrics & Gynaecology Information System',         'category' => 'specialist',  'icon' => 'baby',          'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 24, 'tagline' => 'Antenatal care, delivery records, and gynaecology case management.'],
            ['slug' => 'mhis',              'name' => 'MHIS',              'subtitle' => 'Mental Health Information System',                    'category' => 'specialist',  'icon' => 'brain',         'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 25, 'tagline' => 'Mental health assessment, treatment plans, and session notes.'],
            ['slug' => 'ndis',              'name' => 'NDIS',              'subtitle' => 'Nutrition & Dietetics Information System',            'category' => 'specialist',  'icon' => 'apple',         'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 26, 'tagline' => 'Nutritional assessments, dietary plans, and patient progress tracking.'],
            ['slug' => 'ophis',             'name' => 'OPHIS',             'subtitle' => 'Ophthalmology Information System',                    'category' => 'specialist',  'icon' => 'eye',           'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 27, 'tagline' => 'Eye examinations, visual acuity records, and ophthalmic surgery tracking.'],
            ['slug' => 'orthois',           'name' => 'ORTHOIS',           'subtitle' => 'Orthotics & Prosthetics Information System',          'category' => 'specialist',  'icon' => 'accessibility', 'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 28, 'tagline' => 'Orthotic and prosthetic device management and fitting records.'],
            ['slug' => 'paedis',            'name' => 'PAEDIS',            'subtitle' => 'Paediatrics Information System',                      'category' => 'specialist',  'icon' => 'shield-heart',  'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 29, 'tagline' => 'Growth tracking, immunisation records, and paediatric care management.'],
            ['slug' => 'rehabis',           'name' => 'REHABIS',           'subtitle' => 'Physical Medicine & Rehabilitation Information System','category' => 'specialist',  'icon' => 'dumbbell',      'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 30, 'tagline' => 'Rehabilitation plans, therapy sessions, and functional outcome tracking.'],
            ['slug' => 'sltis',             'name' => 'SLTIS',             'subtitle' => 'Speech & Language Therapy Information System',        'category' => 'specialist',  'icon' => 'waves',         'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 31, 'tagline' => 'Speech and language therapy assessment and intervention records.'],
            ['slug' => 'opes-cdms',         'name' => 'OPES CDMS',         'subtitle' => 'Clinical Document Management System',                 'category' => 'specialist',  'icon' => 'folder-open',   'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 32, 'tagline' => 'Centralised clinical document storage, versioning, and audit trail.'],
            ['slug' => 'rcmis',             'name' => 'RCMIS',             'subtitle' => 'Revenue Cycle Management Information System',         'category' => 'specialist',  'icon' => 'receipt',       'color' => '#94a3b8', 'is_featured' => false, 'sort_order' => 33, 'tagline' => 'End-to-end revenue cycle: billing, claims, and financial reporting.'],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
