<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->string('title', 250);
            $table->foreignId('lead_id')->nullable()->nullOnDelete()->constrained('leads');
            $table->enum('stage', ['prospecting','qualification','proposal','negotiation','closed_won','closed_lost'])->default('prospecting');
            $table->decimal('value', 12, 2)->default(0);
            $table->string('currency', 10)->default('XAF');
            $table->unsignedTinyInteger('probability')->default(50);
            $table->date('expected_close_date')->nullable();
            $table->date('actual_close_date')->nullable();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->string('lost_reason', 300)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
