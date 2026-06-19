<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->date('week_start');
            $table->date('week_end');
            $table->json('metrics');
            $table->text('summary')->nullable();
            $table->text('action_items')->nullable();
            $table->foreignId('author_id')->constrained('users');
            $table->timestamp('generated_at');
            $table->timestamps();
            $table->unique(['cohort_id', 'week_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_reviews');
    }
};
