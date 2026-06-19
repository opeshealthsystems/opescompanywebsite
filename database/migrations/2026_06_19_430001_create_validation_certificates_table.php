<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validation_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_member_id')->constrained('cohort_members')->cascadeOnDelete();
            $table->foreignId('final_evaluation_id')->nullable()->constrained('final_evaluations')->nullOnDelete();
            $table->string('certificate_number');
            $table->unsignedInteger('score');
            $table->string('tier', 20); // distinction|pass
            $table->foreignId('issued_by')->constrained('users');
            $table->timestamp('issued_at');
            $table->timestamps();
            $table->unique('cohort_member_id');
            $table->unique('certificate_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validation_certificates');
    }
};
