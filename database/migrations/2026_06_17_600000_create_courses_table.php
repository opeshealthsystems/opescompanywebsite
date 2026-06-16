<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('product_slug', 100)->nullable();
            $table->string('title', 200);
            $table->string('title_fr', 200)->nullable();
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->text('description_fr')->nullable();
            $table->string('level', 20)->default('beginner'); // beginner|intermediate|advanced
            $table->unsignedSmallInteger('duration_hours')->nullable();
            $table->string('cover_image', 300)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
