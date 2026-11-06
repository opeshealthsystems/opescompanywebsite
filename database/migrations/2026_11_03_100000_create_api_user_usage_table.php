<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_user_usage', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id');
            $table->unsignedBigInteger('user_id');
            $table->date('usage_date');
            $table->unsignedBigInteger('requests')->default(0);
            $table->unique(['client_id', 'user_id', 'usage_date']);
            $table->index(['user_id', 'client_id', 'usage_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_user_usage');
    }
};
