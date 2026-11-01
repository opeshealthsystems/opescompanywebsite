<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_command_receipts', function (Blueprint $table) {
            $table->id(); $table->char('key_hash', 64)->unique(); $table->char('fingerprint', 64);
            $table->unsignedBigInteger('actor_id')->nullable(); $table->string('operation', 160);
            $table->text('outcome'); $table->timestamp('expires_at')->index(); $table->timestamps();
        });
        Schema::create('business_outbox', function (Blueprint $table) {
            $table->uuid('id')->primary(); $table->string('kind', 24); $table->string('type', 180);
            $table->unsignedBigInteger('actor_id')->nullable(); $table->text('payload');
            $table->string('status', 20)->default('pending'); $table->uuid('claim_token')->nullable();
            $table->unsignedInteger('attempts')->default(0); $table->timestamp('locked_at')->nullable();
            $table->timestamp('available_at')->nullable(); $table->timestamp('completed_at')->nullable();
            $table->string('error_type')->nullable(); $table->timestamps(); $table->index(['status', 'available_at']);
        });
        Schema::create('business_event_inbox', function (Blueprint $table) {
            $table->id(); $table->uuid('event_id'); $table->string('consumer', 100);
            $table->timestamp('processed_at'); $table->unique(['event_id', 'consumer']);
        });
        Schema::create('business_activity', function (Blueprint $table) {
            $table->uuid('id')->primary(); $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->string('operation', 180); $table->json('references'); $table->timestamp('occurred_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_activity'); Schema::dropIfExists('business_event_inbox');
        Schema::dropIfExists('business_outbox'); Schema::dropIfExists('business_command_receipts');
    }
};
