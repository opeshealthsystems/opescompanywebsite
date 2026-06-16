<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('title_fr', 200)->nullable();
            $table->text('description')->nullable();
            $table->text('description_fr')->nullable();
            $table->string('audience', 20)->default('all'); // practitioners|customers|all
            $table->string('status', 20)->default('draft'); // draft|active|closed
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
