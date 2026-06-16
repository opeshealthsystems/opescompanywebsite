<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->string('review_period', 50);
            $table->date('review_date');
            $table->unsignedTinyInteger('overall_rating')->default(3);
            $table->unsignedTinyInteger('goals_rating')->default(3);
            $table->unsignedTinyInteger('teamwork_rating')->default(3);
            $table->unsignedTinyInteger('technical_rating')->default(3);
            $table->unsignedTinyInteger('communication_rating')->default(3);
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('goals_for_next_period')->nullable();
            $table->text('employee_comments')->nullable();
            $table->enum('status', ['draft','submitted','acknowledged'])->default('draft');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('performance_reviews'); }
};
