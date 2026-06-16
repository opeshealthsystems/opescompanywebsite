<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practitioner_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practitioner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('practitioner_programs')->cascadeOnDelete();
            $table->text('motivation')->nullable();
            $table->string('status', 20)->default('pending'); // pending|approved|rejected|withdrawn
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->unique(['practitioner_id', 'program_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practitioner_applications');
    }
};
