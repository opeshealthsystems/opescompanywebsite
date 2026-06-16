<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practitioner_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('profession', 50);
            $table->string('specialty', 120)->nullable();
            $table->string('workplace_name', 150)->nullable();
            $table->string('workplace_city', 80)->nullable();
            $table->string('workplace_country', 80)->default('CM');
            $table->string('registration_number', 60)->nullable();
            $table->unsignedSmallInteger('years_of_experience')->nullable();
            $table->text('bio')->nullable();
            $table->text('opes_testimonial')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practitioner_profiles');
    }
};
