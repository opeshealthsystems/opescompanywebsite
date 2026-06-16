<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->string('title', 250);
            $table->text('description')->nullable();
            $table->enum('status', ['planning','active','on_hold','completed','cancelled'])->default('planning');
            $table->enum('priority', ['low','medium','high','critical'])->default('medium');
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 14, 2)->default(0);
            $table->string('currency', 10)->default('XAF');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
