<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advisory_council_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('validation_certificate_id')->nullable()->constrained('validation_certificates')->nullOnDelete();
            $table->string('title');
            $table->date('term_start');
            $table->date('term_end')->nullable();
            $table->string('status', 20)->default('active'); // active|inactive
            $table->foreignId('invited_by')->constrained('users');
            $table->timestamp('invited_at');
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advisory_council_members');
    }
};
