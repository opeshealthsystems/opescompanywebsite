<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_opening_id')->constrained()->cascadeOnDelete();
            $table->string('applicant_name', 200);
            $table->string('email', 150);
            $table->string('phone', 50)->nullable();
            $table->enum('status', ['received','reviewing','shortlisted','interviewed','offered','hired','rejected'])->default('received');
            $table->string('resume_path', 300)->nullable();
            $table->date('applied_at')->nullable();
            $table->date('interview_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('job_applications'); }
};
