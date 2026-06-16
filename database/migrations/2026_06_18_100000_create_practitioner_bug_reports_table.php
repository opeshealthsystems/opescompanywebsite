<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practitioner_bug_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practitioner_id')->constrained('users')->cascadeOnDelete();
            $table->string('product_slug', 100)->nullable();
            $table->string('title', 200);
            $table->string('severity', 20)->default('medium'); // low|medium|high|critical
            $table->text('description');
            $table->text('steps_to_reproduce')->nullable();
            $table->string('screenshot_url', 500)->nullable();
            $table->string('status', 20)->default('open'); // open|triaged|in_progress|resolved|closed|wont_fix
            $table->text('admin_response')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practitioner_bug_reports');
    }
};
