<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospital_onboardings', function (Blueprint $table) {
            $table->id();

            // Facility
            $table->string('facility_name');
            $table->string('facility_type', 40)->nullable();
            $table->string('facility_size', 20)->nullable();
            $table->string('country', 60)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('website', 150)->nullable();
            $table->string('registration_number', 80)->nullable();

            // Primary contact (the champion)
            $table->string('contact_name', 120);
            $table->string('contact_title', 80)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('contact_email', 150);

            // Scope of the demo
            $table->json('products')->nullable();
            $table->text('departments')->nullable();
            $table->date('preferred_start_date')->nullable();
            $table->text('notes')->nullable();

            // Meta / workflow
            $table->string('locale', 5)->default('en');
            $table->string('status', 20)->default('new');
            $table->text('admin_notes')->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();
        });

        Schema::create('onboarding_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_onboarding_id')
                ->constrained('hospital_onboardings')
                ->cascadeOnDelete();

            $table->string('full_name', 120);
            $table->string('job_role', 40)->nullable();
            $table->string('department', 80)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('access_level', 40)->nullable();

            // Captured from the requester: does this person handle/take tasks in the system?
            $table->boolean('takes_tasks')->default(false);

            // Filled by the OPES team once provisioned in the separate EMR SaaS.
            $table->boolean('emr_account_created')->default(false);
            $table->string('emr_username', 120)->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_staff');
        Schema::dropIfExists('hospital_onboardings');
    }
};
