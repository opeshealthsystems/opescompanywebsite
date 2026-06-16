<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practitioner_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('practitioner_applications')->cascadeOnDelete();
            $table->foreignId('practitioner_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('wait_time_rating')->nullable();
            $table->unsignedTinyInteger('data_integrity_rating')->nullable();
            $table->unsignedTinyInteger('usability_rating')->nullable();
            $table->unsignedTinyInteger('overall_rating')->nullable();
            $table->text('findings_text')->nullable();
            $table->string('video_url', 500)->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practitioner_findings');
    }
};
