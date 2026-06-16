<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->unsignedBigInteger('department_id')->nullable();
            $table->enum('type', ['full_time','part_time','contract','internship','remote'])->default('full_time');
            $table->string('location', 150)->nullable();
            $table->enum('status', ['open','paused','closed','filled'])->default('open');
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->unsignedSmallInteger('openings_count')->default(1);
            $table->date('posted_at')->nullable();
            $table->date('closes_at')->nullable();
            $table->string('salary_range', 100)->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('job_openings'); }
};
