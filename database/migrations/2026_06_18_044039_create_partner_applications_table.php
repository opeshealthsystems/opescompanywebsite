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
        Schema::create('partner_applications', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name');
            $table->string('contact_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('country');
            $table->string('city')->nullable();
            $table->string('partner_type');
            $table->string('organization_type')->nullable();
            $table->string('annual_revenue_range')->nullable();
            $table->text('target_market')->nullable();
            $table->text('description');
            $table->string('website')->nullable();
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
        Schema::dropIfExists('partner_applications');
    }
};
