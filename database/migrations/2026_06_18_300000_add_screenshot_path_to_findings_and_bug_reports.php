<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practitioner_findings', function (Blueprint $table) {
            $table->string('screenshot_path', 300)->nullable()->after('video_url');
        });

        Schema::table('practitioner_bug_reports', function (Blueprint $table) {
            $table->string('screenshot_path', 300)->nullable()->after('screenshot_url');
        });
    }

    public function down(): void
    {
        Schema::table('practitioner_findings', function (Blueprint $table) {
            $table->dropColumn('screenshot_path');
        });

        Schema::table('practitioner_bug_reports', function (Blueprint $table) {
            $table->dropColumn('screenshot_path');
        });
    }
};
