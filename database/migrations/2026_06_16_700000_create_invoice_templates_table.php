<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->foreignId('customer_id')->nullable()->nullOnDelete()->constrained('users');
            $table->string('client_name', 200)->nullable()->comment('Manual client name if no user profile');
            $table->string('client_email', 200)->nullable();
            $table->enum('frequency', ['weekly', 'monthly', 'quarterly', 'semi_annual', 'annual'])->default('monthly');
            $table->date('next_due_date');
            $table->date('end_date')->nullable()->comment('Stop generating after this date');
            $table->unsignedSmallInteger('payment_terms_days')->default(30);
            $table->string('currency', 10)->default('XAF');
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->json('line_items')->comment('Array of {description, quantity, unit_price}');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_templates');
    }
};
