<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_application_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('membership_role', 20)->default('member');
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['partner_application_id', 'user_id'], 'partner_application_member_unique');
            $table->index(['user_id', 'partner_application_id'], 'partner_member_account_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_application_memberships');
    }
};
