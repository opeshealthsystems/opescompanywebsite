<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validation_test_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('validation_workflow_id')->constrained('validation_workflows')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('steps')->nullable();
            $table->text('expected_result')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validation_test_cases');
    }
};
