<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validation_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('validation_module_id')->constrained('validation_modules')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['validation_module_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validation_workflows');
    }
};
