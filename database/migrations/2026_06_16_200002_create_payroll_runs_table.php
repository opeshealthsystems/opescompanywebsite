<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['draft', 'processing', 'completed', 'cancelled'])->default('draft');
            $table->decimal('total_gross', 14, 2)->default(0);
            $table->decimal('total_net', 14, 2)->default(0);
            $table->string('currency', 10)->default('XAF');
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_runs');
    }
};
