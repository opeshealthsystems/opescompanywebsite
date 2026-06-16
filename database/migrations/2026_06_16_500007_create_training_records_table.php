<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('training_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->string('provider', 150)->nullable();
            $table->enum('category', ['compliance','technical','soft_skills','safety','clinical','management','other'])->default('other');
            $table->enum('status', ['planned','in_progress','completed','expired'])->default('planned');
            $table->date('start_date')->nullable();
            $table->date('completed_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('certificate_path', 300)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('training_records'); }
};
