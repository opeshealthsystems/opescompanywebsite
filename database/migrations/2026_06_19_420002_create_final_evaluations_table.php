<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_member_id')->constrained('cohort_members')->cascadeOnDelete()->unique();
            $table->json('metrics');
            $table->text('assessment');
            $table->string('rating', 20); // outstanding|strong|satisfactory|needs_improvement
            $table->text('recommendation')->nullable();
            $table->foreignId('evaluator_id')->constrained('users');
            $table->timestamp('evaluated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_evaluations');
    }
};
