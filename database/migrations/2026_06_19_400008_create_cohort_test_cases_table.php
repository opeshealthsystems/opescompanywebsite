<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohort_test_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->foreignId('validation_test_case_id')->constrained('validation_test_cases')->cascadeOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamps();
            $table->unique(['cohort_id', 'validation_test_case_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohort_test_cases');
    }
};
