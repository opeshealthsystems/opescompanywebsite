<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payroll_deduction_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 30)->unique();
            $table->enum('calculation_type', ['percentage','fixed'])->default('percentage');
            $table->decimal('rate', 8, 4)->default(0)->comment('Percentage (e.g. 2.8 = 2.8%) or fixed amount');
            $table->text('description')->nullable();
            $table->boolean('apply_by_default')->default(true)->comment('Auto-add when generating payroll entries');
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('payroll_deduction_types'); }
};
