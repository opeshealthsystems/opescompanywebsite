<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospital_onboardings', function (Blueprint $table) {
            // A. Authorisation & consent
            $table->string('owner_name', 120)->nullable();
            $table->string('owner_title', 80)->nullable();
            $table->string('owner_phone', 30)->nullable();
            $table->string('owner_email', 150)->nullable();
            $table->string('completed_by_role', 80)->nullable();
            $table->boolean('completer_authorised')->default(false);
            $table->boolean('data_sharing_consent')->default(false);

            // B. Clinic identity (facility_name kept as the common/display name)
            $table->string('legal_name', 160)->nullable();
            $table->string('trading_name', 160)->nullable();
            $table->date('registration_date')->nullable();
            $table->string('tax_number', 60)->nullable();
            $table->string('licence_number', 80)->nullable();
            $table->string('licence_authority', 120)->nullable();
            $table->string('gps_location', 120)->nullable();
            $table->string('opening_hours', 160)->nullable();
            $table->string('working_language', 10)->nullable();

            // C. Services
            $table->json('services')->nullable();
            $table->unsignedSmallInteger('beds')->nullable();
            $table->unsignedSmallInteger('consulting_rooms')->nullable();
            $table->unsignedInteger('patients_per_day')->nullable();
            $table->text('insurers')->nullable();
            $table->text('mobile_money')->nullable();
            $table->string('vat_rate', 20)->nullable();
            $table->string('currency', 10)->nullable();

            // D. Setup
            $table->text('devices')->nullable();
            $table->text('connectivity_notes')->nullable();
            $table->string('email_domain_choice', 20)->nullable(); // own | ours
            $table->string('email_domain', 120)->nullable();
            $table->text('current_software')->nullable();
            $table->text('training_attendees')->nullable();

            // F. Roles
            $table->string('system_admin', 120)->nullable();
            $table->text('records_viewers')->nullable();
            $table->text('approval_authority')->nullable();

            // G. Documents (single-file paths, stored privately)
            $table->string('logo_path')->nullable();
            $table->string('stamp_path')->nullable();
            $table->string('letterhead_path')->nullable();
            $table->string('signed_agreement_path')->nullable();
            $table->string('service_price_list_path')->nullable();
            $table->string('drug_stock_path')->nullable();
            $table->string('lab_test_list_path')->nullable();
            $table->string('pharmacy_licence_path')->nullable();
            $table->string('lab_licence_path')->nullable();

            // G. Patient data readiness (no PHI uploaded here, by design)
            $table->string('patient_data_format', 40)->nullable();
            $table->unsignedInteger('patient_record_count')->nullable();
            $table->text('patient_data_notes')->nullable();
        });

        Schema::create('onboarding_sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_onboarding_id')
                ->constrained('hospital_onboardings')
                ->cascadeOnDelete();
            $table->string('name', 160);
            $table->string('address')->nullable();
            $table->timestamps();
        });

        Schema::table('onboarding_staff', function (Blueprint $table) {
            $table->string('specialty', 120)->nullable()->after('job_role');
            $table->string('licence_number', 80)->nullable();
            $table->string('licence_issuing_body', 120)->nullable();
            $table->date('licence_expiry')->nullable();
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->string('site', 160)->nullable();
            $table->string('working_hours', 160)->nullable();
            $table->string('consultation_fee', 40)->nullable();
            $table->string('signature_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_staff', function (Blueprint $table) {
            $table->dropColumn([
                'specialty', 'licence_number', 'licence_issuing_body', 'licence_expiry',
                'years_experience', 'site', 'working_hours', 'consultation_fee', 'signature_path',
            ]);
        });

        Schema::dropIfExists('onboarding_sites');

        Schema::table('hospital_onboardings', function (Blueprint $table) {
            $table->dropColumn([
                'owner_name', 'owner_title', 'owner_phone', 'owner_email', 'completed_by_role',
                'completer_authorised', 'data_sharing_consent',
                'legal_name', 'trading_name', 'registration_date', 'tax_number', 'licence_number',
                'licence_authority', 'gps_location', 'opening_hours', 'working_language',
                'services', 'beds', 'consulting_rooms', 'patients_per_day', 'insurers',
                'mobile_money', 'vat_rate', 'currency',
                'devices', 'connectivity_notes', 'email_domain_choice', 'email_domain',
                'current_software', 'training_attendees',
                'system_admin', 'records_viewers', 'approval_authority',
                'logo_path', 'stamp_path', 'letterhead_path', 'signed_agreement_path',
                'service_price_list_path', 'drug_stock_path', 'lab_test_list_path',
                'pharmacy_licence_path', 'lab_licence_path',
                'patient_data_format', 'patient_record_count', 'patient_data_notes',
            ]);
        });
    }
};
