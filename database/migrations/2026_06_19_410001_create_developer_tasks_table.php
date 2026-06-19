<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('developer_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_report_id')->constrained('issue_reports')->cascadeOnDelete()->unique();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('priority', 10); // critical|high|medium|low
            $table->string('status', 20)->default('open'); // open|in_progress|fixed|reopened|wont_fix
            $table->text('resolution_notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('fixed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developer_tasks');
    }
};
