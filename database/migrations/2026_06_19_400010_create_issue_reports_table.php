<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_member_id')->constrained('cohort_members')->cascadeOnDelete();
            $table->foreignId('daily_test_session_id')->nullable()->constrained('daily_test_sessions')->nullOnDelete();
            $table->foreignId('validation_product_id')->constrained('validation_products')->restrictOnDelete();
            $table->foreignId('validation_module_id')->constrained('validation_modules')->restrictOnDelete();
            $table->foreignId('validation_workflow_id')->constrained('validation_workflows')->restrictOnDelete();
            $table->foreignId('validation_test_case_id')->nullable()->constrained('validation_test_cases')->nullOnDelete();
            $table->string('title');
            $table->string('issue_type', 30); // bug|missing_feature|workflow_problem|clinical_risk|ui_ux_problem|performance_issue|security_concern|interoperability_issue|data_quality_issue|recommendation
            $table->string('severity', 10); // critical|high|medium|low
            $table->text('description');
            $table->text('steps_to_reproduce');
            $table->text('expected_result');
            $table->text('actual_result');
            $table->text('clinical_impact');
            $table->text('recommendation')->nullable();
            $table->json('attachments')->nullable();
            $table->string('status', 30)->default('submitted'); // submitted|clinical_review|product_review|accepted|rejected|duplicate|needs_more_information|sent_to_development|fixed|closed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_reports');
    }
};
