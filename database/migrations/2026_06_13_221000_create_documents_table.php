<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_template_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['receipt', 'letterhead', 'contract_employee', 'contract_business']);
            $table->string('title');
            $table->string('reference_number')->unique();
            $table->longText('body_rendered');
            $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('addressee_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('addressee_name');
            $table->string('addressee_email')->nullable();
            $table->enum('status', ['draft', 'sent', 'pending_signature', 'signed', 'voided'])->default('draft');
            $table->boolean('requires_signature')->default(false);
            $table->string('signature_token', 64)->nullable()->unique();
            $table->timestamp('signature_token_expires_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('signed_by_name')->nullable();
            $table->string('signed_ip', 45)->nullable();
            $table->json('signed_data')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
