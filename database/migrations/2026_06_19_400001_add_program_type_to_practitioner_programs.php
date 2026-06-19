<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practitioner_programs', function (Blueprint $table) {
            $table->string('program_type', 20)->default('general')->after('type'); // general|validation
        });
    }

    public function down(): void
    {
        Schema::table('practitioner_programs', function (Blueprint $table) {
            $table->dropColumn('program_type');
        });
    }
};
