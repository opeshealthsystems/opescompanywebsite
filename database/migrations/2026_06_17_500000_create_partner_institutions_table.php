<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('name_fr', 200)->nullable();
            $table->string('type', 30)->default('university'); // university|research_institute|ngo|government|hospital_network|other
            $table->string('country', 80)->default('CM');
            $table->string('city', 80)->nullable();
            $table->string('website', 300)->nullable();
            $table->string('logo', 300)->nullable();
            $table->text('description')->nullable();
            $table->text('description_fr')->nullable();
            $table->year('partnership_since')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_institutions');
    }
};
