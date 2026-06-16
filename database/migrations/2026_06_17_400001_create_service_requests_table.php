<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 30)->default('maintenance'); // installation|maintenance|training|other
            $table->string('product_slug', 100)->nullable();
            $table->text('description')->nullable();
            $table->date('preferred_date');
            $table->string('preferred_time', 10)->nullable();
            $table->string('location', 200)->nullable();
            $table->string('status', 20)->default('pending'); // pending|confirmed|assigned|completed|cancelled
            $table->foreignId('assigned_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('confirmed_date')->nullable();
            $table->string('confirmed_time', 10)->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('reference_number', 20)->unique()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
