<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_bills', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->foreignId('vendor_id')->nullable()->nullOnDelete()->constrained('vendors');
            $table->string('vendor_name', 150)->nullable();
            $table->foreignId('purchase_order_id')->nullable()->nullOnDelete()->constrained('purchase_orders');
            $table->string('bill_number', 100)->nullable();
            $table->enum('status', ['draft','received','approved','paid','overdue','disputed'])->default('received');
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('currency', 10)->default('XAF');
            $table->date('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_bills');
    }
};
