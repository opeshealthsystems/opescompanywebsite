<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('name_fr')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('subtitle_fr')->nullable();
            $table->enum('category', ['core', 'diagnostics', 'specialist'])->default('core');
            $table->text('tagline')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
