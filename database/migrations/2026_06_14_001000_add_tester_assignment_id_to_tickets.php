<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('tester_assignment_id')
                ->nullable()
                ->after('closed_at')
                ->constrained('tester_assignments')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['tester_assignment_id']);
            $table->dropColumn('tester_assignment_id');
        });
    }
};
