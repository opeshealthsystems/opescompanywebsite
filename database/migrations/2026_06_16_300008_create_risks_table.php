<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->string('title', 250);
            $table->text('description')->nullable();
            $table->enum('category', ['operational','financial','technical','legal','strategic','reputational'])->default('operational');
            $table->enum('likelihood', ['very_low','low','medium','high','very_high'])->default('medium');
            $table->enum('impact', ['very_low','low','medium','high','very_high'])->default('medium');
            $table->unsignedTinyInteger('risk_score')->default(9);
            $table->enum('status', ['open','mitigated','accepted','closed'])->default('open');
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->text('mitigation_plan')->nullable();
            $table->date('review_date')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('risks'); }
};
