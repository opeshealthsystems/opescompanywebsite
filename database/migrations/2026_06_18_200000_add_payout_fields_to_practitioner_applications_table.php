<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practitioner_applications', function (Blueprint $table) {
            $table->string('payout_status', 20)->default('not_applicable')->after('admin_notes'); // not_applicable|pending|paid
            $table->decimal('payout_amount', 10, 2)->nullable()->after('payout_status');
            $table->string('payout_currency', 3)->default('XAF')->after('payout_amount');
            $table->string('payout_reference', 60)->nullable()->after('payout_currency');
            $table->timestamp('paid_at')->nullable()->after('payout_reference');
        });
    }

    public function down(): void
    {
        Schema::table('practitioner_applications', function (Blueprint $table) {
            $table->dropColumn([
                'payout_status',
                'payout_amount',
                'payout_currency',
                'payout_reference',
                'paid_at',
            ]);
        });
    }
};
