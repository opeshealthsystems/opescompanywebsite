<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practitioner_applications', function (Blueprint $table) {
            $table->string('payout_provider', 20)->nullable()->after('payout_reference');
            $table->timestamp('payout_initiated_at')->nullable()->after('payout_provider');
            $table->string('payout_failure_reason', 255)->nullable()->after('payout_initiated_at');
        });
    }

    public function down(): void
    {
        Schema::table('practitioner_applications', function (Blueprint $table) {
            $table->dropColumn(['payout_provider', 'payout_initiated_at', 'payout_failure_reason']);
        });
    }
};
