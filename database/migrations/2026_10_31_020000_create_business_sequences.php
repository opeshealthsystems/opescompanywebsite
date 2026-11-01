<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('business_sequences', function (Blueprint $table) {
            $table->string('name', 100)->primary(); $table->unsignedBigInteger('last_value')->default(0);
        });
    }
    public function down(): void { Schema::dropIfExists('business_sequences'); }
};
