<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('salary_grade_id')->nullable()->after('department_id');
            $table->decimal('base_salary', 12, 2)->nullable()->after('salary_grade_id');
            $table->string('salary_currency', 10)->default('XAF')->after('base_salary');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['salary_grade_id', 'base_salary', 'salary_currency']);
        });
    }
};
