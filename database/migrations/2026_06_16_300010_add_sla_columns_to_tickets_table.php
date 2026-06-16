<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('sla_rule_id')->nullable()->nullOnDelete()->constrained('sla_rules')->after('closed_at');
            $table->timestamp('sla_response_due_at')->nullable()->after('sla_rule_id');
            $table->timestamp('sla_resolution_due_at')->nullable()->after('sla_response_due_at');
        });
    }
    public function down(): void {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['sla_rule_id']);
            $table->dropColumn(['sla_rule_id','sla_response_due_at','sla_resolution_due_at']);
        });
    }
};
