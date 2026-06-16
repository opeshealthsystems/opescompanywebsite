<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sla_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('ticket_type', 50)->nullable();
            $table->enum('ticket_priority', ['low','medium','high','urgent']);
            $table->unsignedSmallInteger('response_time_hours')->default(24);
            $table->unsignedSmallInteger('resolution_time_hours')->default(72);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('sla_rules'); }
};
