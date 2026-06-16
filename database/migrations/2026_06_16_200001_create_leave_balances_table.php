<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->enum('type', ['annual', 'sick', 'unpaid', 'maternity', 'paternity', 'other']);
            $table->decimal('entitled_days', 5, 1)->default(0);
            $table->decimal('used_days', 5, 1)->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'year', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
