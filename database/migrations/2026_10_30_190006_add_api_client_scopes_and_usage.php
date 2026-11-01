<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) { $table->json('scopes')->nullable(); });
        Schema::create('api_client_usage', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id');
            $table->date('usage_date');
            $table->unsignedBigInteger('requests')->default(0);
            $table->unique(['client_id', 'usage_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_client_usage');
        Schema::table('oauth_clients', function (Blueprint $table) { $table->dropColumn('scopes'); });
    }
};
