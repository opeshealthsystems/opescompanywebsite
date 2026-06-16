<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->string('title', 250);
            $table->foreignId('lead_id')->nullable()->nullOnDelete()->constrained('leads');
            $table->enum('type', ['service_agreement','nda','sla','partnership','vendor','employment','other'])->default('service_agreement');
            $table->enum('status', ['draft','sent','active','expired','terminated','renewed'])->default('draft');
            $table->decimal('value', 12, 2)->default(0);
            $table->string('currency', 10)->default('XAF');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
