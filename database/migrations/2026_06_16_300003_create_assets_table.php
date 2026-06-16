<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->string('name', 200);
            $table->enum('category', ['laptop','desktop','mobile','server','furniture','vehicle','software_license','other'])->default('other');
            $table->string('serial_number', 100)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('current_value', 12, 2)->default(0);
            $table->string('currency', 10)->default('XAF');
            $table->string('location', 150)->nullable();
            $table->foreignId('assigned_to')->nullable()->nullOnDelete()->constrained('users');
            $table->enum('status', ['active','in_repair','retired','disposed'])->default('active');
            $table->date('warranty_expires')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('assets'); }
};
