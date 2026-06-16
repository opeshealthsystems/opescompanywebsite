<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->enum('category', [
                'payroll','rent','utilities','software','hardware',
                'travel','marketing','legal','training','other'
            ]);
            $table->string('department', 100)->default('General');
            $table->decimal('allocated_amount', 14, 2);
            $table->string('currency', 10)->default('XAF');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['year', 'category', 'department']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
