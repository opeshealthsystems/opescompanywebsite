<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_fr')->nullable();
            $table->string('slug')->unique();
            $table->string('excerpt')->nullable();
            $table->string('excerpt_fr')->nullable();
            $table->longText('body');
            $table->longText('body_fr')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('category')->default('Digital Health');
            $table->string('author')->default('OPES Health Systems');
            $table->boolean('published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
