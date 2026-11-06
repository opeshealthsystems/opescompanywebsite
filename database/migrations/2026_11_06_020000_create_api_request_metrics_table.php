<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_request_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('client_id', 80);
            $table->string('principal_type', 12);
            $table->string('principal_id', 80);
            $table->date('metric_date');
            $table->string('route_key', 180);
            $table->string('http_method', 10);
            $table->unsignedBigInteger('requests')->default(0);
            $table->unsignedBigInteger('responses_2xx')->default(0);
            $table->unsignedBigInteger('responses_3xx')->default(0);
            $table->unsignedBigInteger('responses_4xx')->default(0);
            $table->unsignedBigInteger('responses_5xx')->default(0);
            $table->unsignedBigInteger('total_response_time_ms')->default(0);
            $table->unsignedInteger('max_response_time_ms')->default(0);
            $table->timestamps();

            $table->unique(
                ['client_id', 'principal_type', 'principal_id', 'metric_date', 'route_key', 'http_method'],
                'api_request_metrics_dimension_unique'
            );
            $table->index(
                ['client_id', 'principal_type', 'principal_id', 'metric_date'],
                'api_request_metrics_period_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_request_metrics');
    }
};
