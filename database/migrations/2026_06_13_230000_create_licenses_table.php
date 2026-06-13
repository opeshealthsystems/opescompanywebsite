<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
            $table->string('product_slug');
            $table->string('product_name');
            $table->string('license_key')->unique();
            $table->string('plan')->default('standard');
            $table->unsignedSmallInteger('seats')->default(1);
            $table->enum('status', ['active', 'suspended', 'expired', 'cancelled'])->default('active');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('price')->nullable();
            $table->string('currency', 10)->default('XAF');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
