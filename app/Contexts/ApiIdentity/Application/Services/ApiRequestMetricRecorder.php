<?php

namespace App\Contexts\ApiIdentity\Application\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/** Stores aggregate counters only; never records URLs, query strings, headers, bodies or identifiers. */
final class ApiRequestMetricRecorder
{
    public function record(Request $request, string $clientId, string $principalType, string $principalId, int $status, int $durationMs): void
    {
        if (!in_array($principalType, ['machine', 'user'], true)) return;

        $routeKey = $request->route()?->getName();
        if (!is_string($routeKey) || $routeKey === '') {
            $routeKey = strtolower($request->method()).' '.($request->route()?->uri() ?? 'unmatched');
        }
        $routeKey = mb_substr($routeKey, 0, 180);
        $method = mb_substr(strtoupper($request->method()), 0, 10);
        $date = now('UTC')->toDateString();
        $this->pruneExpiredAggregates($date);
        $statusColumn = match (true) {
            $status >= 500 => 'responses_5xx',
            $status >= 400 => 'responses_4xx',
            $status >= 300 => 'responses_3xx',
            default => 'responses_2xx',
        };

        try {
            DB::transaction(function () use ($clientId, $principalType, $principalId, $date, $routeKey, $method, $statusColumn, $durationMs): void {
                $dimensions = [
                    'client_id' => $clientId,
                    'principal_type' => $principalType,
                    'principal_id' => $principalId,
                    'metric_date' => $date,
                    'route_key' => $routeKey,
                    'http_method' => $method,
                ];

                DB::table('api_request_metrics')->insertOrIgnore($dimensions + [
                    'requests' => 0, 'responses_2xx' => 0, 'responses_3xx' => 0,
                    'responses_4xx' => 0, 'responses_5xx' => 0, 'total_response_time_ms' => 0,
                    'max_response_time_ms' => 0, 'created_at' => now(), 'updated_at' => now(),
                ]);

                $row = DB::table('api_request_metrics')->where($dimensions)->lockForUpdate()->first();
                if (!$row) return;

                DB::table('api_request_metrics')->where('id', $row->id)->update([
                    'requests' => $row->requests + 1,
                    $statusColumn => $row->{$statusColumn} + 1,
                    'total_response_time_ms' => $row->total_response_time_ms + max(0, $durationMs),
                    'max_response_time_ms' => max($row->max_response_time_ms, max(0, $durationMs)),
                    'updated_at' => now(),
                ]);
            }, 3);
        } catch (\Throwable $error) {
            // Metrics must not turn a completed API response into a failure.
            Log::warning('API request metric could not be recorded', [
                'route_key' => $routeKey,
                'principal_type' => $principalType,
                'error_type' => $error::class,
            ]);
        }
    }

    private function pruneExpiredAggregates(string $date): void
    {
        try {
            if (!Cache::add('api-request-metrics-pruned:'.$date, true, now('UTC')->endOfDay())) return;

            DB::table('api_request_metrics')
                ->where('metric_date', '<', \Carbon\CarbonImmutable::parse($date, 'UTC')->subDays(35)->toDateString())
                ->delete();
        } catch (\Throwable $error) {
            // Retention cleanup is best-effort and must never block an API response.
            Log::warning('API request metric retention cleanup could not complete', [
                'error_type' => $error::class,
            ]);
        }
    }

    public static function durationMilliseconds(int $startedAt): int
    {
        return max(0, (int) round((hrtime(true) - $startedAt) / 1_000_000));
    }

    public static function exceptionStatus(\Throwable $error): int
    {
        if ($error instanceof \Illuminate\Validation\ValidationException) return Response::HTTP_UNPROCESSABLE_ENTITY;
        if ($error instanceof \Illuminate\Auth\Access\AuthorizationException) return Response::HTTP_FORBIDDEN;
        if ($error instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) return $error->getStatusCode();
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
