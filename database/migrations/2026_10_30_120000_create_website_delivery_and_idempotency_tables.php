<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_form_deliveries', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id')->unique();
            $table->string('event_type', 80);
            $table->string('submission_type', 160);
            $table->unsignedBigInteger('submission_id');
            $table->string('recipient', 191);
            $table->string('status', 20)->default('pending');
            $table->unsignedInteger('attempts')->default(0);
            $table->uuid('claim_token')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('next_attempt_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('message_id')->nullable();
            $table->string('error_type')->nullable();
            $table->timestamps();
            $table->unique(['submission_type', 'submission_id', 'recipient'], 'website_delivery_source_recipient_unique');
            $table->index(['status', 'next_attempt_at']);
        });
        Schema::create('api_idempotency_records', function (Blueprint $table) {
            $table->id();
            $table->char('key_hash', 64)->unique();
            $table->char('fingerprint', 64);
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->json('response')->nullable();
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_idempotency_records');
        Schema::dropIfExists('website_form_deliveries');
    }
};
