<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->unsignedInteger('daily_request_limit')->nullable();
            $table->unsignedInteger('monthly_request_limit')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn(['daily_request_limit', 'monthly_request_limit']);
        });
    }
};
