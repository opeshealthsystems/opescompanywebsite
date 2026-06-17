<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practitioner_profiles', function (Blueprint $table) {
            $table->string('payout_number', 20)->nullable()->after('registration_number');
        });
    }

    public function down(): void
    {
        Schema::table('practitioner_profiles', function (Blueprint $table) {
            $table->dropColumn('payout_number');
        });
    }
};
