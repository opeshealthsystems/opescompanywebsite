<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 100)->unique();
            $table->enum('type', ['welcome','invoice_sent','invoice_reminder','ticket_created','ticket_reply','leave_approval','leave_rejection','password_reset','announcement','contract_sent','quote_sent','general'])->default('general');
            $table->string('subject', 250);
            $table->text('body');
            $table->json('variables')->nullable()->comment('List of available template variables like {{name}}, {{amount}}');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('email_templates'); }
};
