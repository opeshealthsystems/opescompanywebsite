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
        Schema::create('demo_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('organization_name');
            $table->string('country')->nullable();
            $table->string('institution_type')->nullable();
            $table->string('institution_size')->nullable();
            $table->json('products')->nullable();
            $table->text('message')->nullable();
            $table->date('preferred_date')->nullable();
            $table->string('locale', 5)->default('en');
            $table->string('status', 20)->default('new');
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
        Schema::dropIfExists('demo_requests');
    }
};
