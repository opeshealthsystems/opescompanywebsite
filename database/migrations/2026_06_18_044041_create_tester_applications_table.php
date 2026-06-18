<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tester_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('profession');
            $table->string('specialty')->nullable();
            $table->string('institution_name')->nullable();
            $table->string('country');
            $table->string('city')->nullable();
            $table->unsignedTinyInteger('years_experience')->default(0);
            $table->json('devices')->nullable();
            $table->json('platforms')->nullable();
            $table->text('motivation');
            $table->text('tech_experience')->nullable();
            $table->string('locale', 5)->default('en');
            $table->string('status', 20)->default('pending');
            $table->text('admin_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tester_applications');
    }
};
