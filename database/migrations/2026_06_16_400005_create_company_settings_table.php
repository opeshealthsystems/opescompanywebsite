<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 200)->default('OPES Health Systems');
            $table->string('company_email', 150)->nullable();
            $table->string('company_phone', 50)->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_website', 200)->nullable();
            $table->string('default_currency', 10)->default('XAF');
            $table->unsignedTinyInteger('fiscal_year_start_month')->default(1);
            $table->string('invoice_prefix', 20)->default('INV');
            $table->string('quote_prefix', 20)->default('QTE');
            $table->decimal('default_tax_rate', 5, 2)->default(0.00);
            $table->string('timezone', 50)->default('Africa/Douala');
            $table->string('date_format', 30)->default('d M Y');
            $table->string('logo_path', 300)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
