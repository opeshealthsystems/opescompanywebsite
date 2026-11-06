<?php

namespace App\Contexts\ApiIdentity\Application\Queries;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/** Caller-scoped 30-day aggregate request metrics for developer operations. */
final class ApiRequestMetricsQueries
{
    public function execute(string $clientId, string $principalType, string $principalId): array
    {
        $today = CarbonImmutable::now('UTC')->startOfDay();
        $start = $today->subDays(29);
        $base = DB::table('api_request_metrics')
            ->where('client_id', $clientId)
            ->where('principal_type', $principalType)
            ->where('principal_id', $principalId)
            ->whereBetween('metric_date', [$start->toDateString(), $today->toDateString()]);

        $totals = (clone $base)->selectRaw(
            'COALESCE(SUM(requests), 0) AS requests, COALESCE(SUM(responses_2xx), 0) AS responses_2xx, '.
            'COALESCE(SUM(responses_3xx), 0) AS responses_3xx, COALESCE(SUM(responses_4xx), 0) AS responses_4xx, '.
            'COALESCE(SUM(responses_5xx), 0) AS responses_5xx, COALESCE(SUM(total_response_time_ms), 0) AS total_response_time_ms, '.
            'COALESCE(MAX(max_response_time_ms), 0) AS max_response_time_ms'
        )->first();

        $dailyRows = (clone $base)->selectRaw(
            'metric_date, SUM(requests) AS requests, SUM(responses_4xx) AS responses_4xx, SUM(responses_5xx) AS responses_5xx'
        )->groupBy('metric_date')->orderBy('metric_date')->get()->keyBy('metric_date');
        $daily = [];
        for ($day = $start; $day <= $today; $day = $day->addDay()) {
            $date = $day->toDateString();
            $row = $dailyRows->get($date);
            $daily[] = [
                'date' => $date,
                'requests' => (int) ($row->requests ?? 0),
                'responses_4xx' => (int) ($row->responses_4xx ?? 0),
                'responses_5xx' => (int) ($row->responses_5xx ?? 0),
            ];
        }

        $operations = (clone $base)->selectRaw(
            'route_key, http_method, SUM(requests) AS requests, SUM(responses_4xx) AS responses_4xx, '.
            'SUM(responses_5xx) AS responses_5xx, SUM(total_response_time_ms) AS total_response_time_ms, '.
            'MAX(max_response_time_ms) AS max_response_time_ms'
        )->groupBy('route_key', 'http_method')->orderByDesc('requests')->limit(25)->get()->map(fn ($row): array => [
            'operation' => $row->route_key,
            'method' => $row->http_method,
            'requests' => (int) $row->requests,
            'responses_4xx' => (int) $row->responses_4xx,
            'responses_5xx' => (int) $row->responses_5xx,
            'average_response_time_ms' => $row->requests ? round($row->total_response_time_ms / $row->requests, 1) : 0,
            'max_response_time_ms' => (int) $row->max_response_time_ms,
        ])->all();

        $requests = (int) $totals->requests;
        return [
            'period_start' => $start->toDateString(),
            'period_end' => $today->toDateString(),
            'requests' => $requests,
            'responses_2xx' => (int) $totals->responses_2xx,
            'responses_3xx' => (int) $totals->responses_3xx,
            'responses_4xx' => (int) $totals->responses_4xx,
            'responses_5xx' => (int) $totals->responses_5xx,
            'average_response_time_ms' => $requests ? round($totals->total_response_time_ms / $requests, 1) : 0,
            'max_response_time_ms' => (int) $totals->max_response_time_ms,
            'daily' => $daily,
            'operations' => $operations,
        ];
    }
}
