<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_test_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_member_id')->constrained('cohort_members')->cascadeOnDelete();
            $table->foreignId('validation_product_id')->constrained('validation_products')->restrictOnDelete();
            $table->foreignId('validation_module_id')->constrained('validation_modules')->restrictOnDelete();
            $table->foreignId('validation_workflow_id')->constrained('validation_workflows')->restrictOnDelete();
            $table->string('facility_context')->nullable();
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedInteger('tasks_completed')->default(0);
            $table->json('screenshots')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_test_sessions');
    }
};
