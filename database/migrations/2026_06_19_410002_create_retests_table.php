<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_report_id')->constrained('issue_reports')->cascadeOnDelete();
            $table->foreignId('developer_task_id')->nullable()->constrained('developer_tasks')->nullOnDelete();
            $table->foreignId('cohort_member_id')->constrained('cohort_members')->cascadeOnDelete();
            $table->string('result', 10); // passed|failed
            $table->text('notes');
            $table->json('attachments')->nullable();
            $table->timestamp('retested_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retests');
    }
};
