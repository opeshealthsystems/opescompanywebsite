<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('knowledge_base_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->nullOnDelete()->constrained('knowledge_base_categories');
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 250);
            $table->string('slug', 250)->unique();
            $table->text('content');
            $table->enum('status', ['draft','published','archived'])->default('draft');
            $table->boolean('is_public')->default(true)->comment('Public = visible in customer portal');
            $table->unsignedInteger('views')->default(0);
            $table->json('tags')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['status','is_public']);
        });
    }
    public function down(): void { Schema::dropIfExists('knowledge_base_articles'); }
};
