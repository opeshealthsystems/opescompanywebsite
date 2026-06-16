<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practitioner_programs', function (Blueprint $table) {
            $table->id();
            $table->string('product_slug', 100)->nullable();
            $table->string('product_name', 150)->nullable();
            $table->string('title', 200);
            $table->string('title_fr', 200)->nullable();
            $table->text('description')->nullable();
            $table->text('description_fr')->nullable();
            $table->string('type', 20)->default('volunteer'); // volunteer|paid
            $table->string('compensation', 100)->nullable();
            $table->unsignedSmallInteger('max_participants')->nullable();
            $table->string('status', 20)->default('draft'); // draft|open|closed|archived
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practitioner_programs');
    }
};
