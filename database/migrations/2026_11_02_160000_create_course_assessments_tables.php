<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('instructions')->nullable();
            $table->unsignedTinyInteger('pass_mark')->default(70);
            $table->unsignedSmallInteger('attempt_limit')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->json('questions');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index(['course_id', 'is_published']);
        });

        Schema::create('course_assessment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('course_assessments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('in_progress');
            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('passed')->nullable();
            $table->json('answers')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->index(['assessment_id', 'user_id', 'status'], 'assessment_attempt_owner_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_assessment_attempts');
        Schema::dropIfExists('course_assessments');
    }
};
