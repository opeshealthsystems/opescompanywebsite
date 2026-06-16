<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', ['present','absent','late','half_day','on_leave','remote','holiday'])->default('present');
            $table->unsignedSmallInteger('hours_worked')->default(0)->comment('Minutes worked, derived from check_in/check_out');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['user_id','date']);
            $table->index('date');
        });
    }
    public function down(): void { Schema::dropIfExists('attendance_records'); }
};
