<?php

namespace App\Contexts\ApiIdentity\Application\Services;

use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/** UTC daily and calendar-month quota accounting for a single machine OAuth client. */
final class MachineClientQuota
{
    public function consume(object $client): array
    {
        if (empty($client->daily_request_limit) && empty($client->monthly_request_limit)) {
            $this->incrementDaily($client->id, CarbonImmutable::now('UTC')->toDateString());
            return ['allowed' => true, 'headers' => []];
        }

        return DB::transaction(function () use ($client): array {
            $locked = DB::table('oauth_clients')->where('id', $client->id)->lockForUpdate()->first();
            abort_unless($locked, 401);

            $now = CarbonImmutable::now('UTC');
            $today = $now->toDateString();
            $monthStart = $now->startOfMonth()->toDateString();
            $dailyLimit = $locked->daily_request_limit === null ? null : (int) $locked->daily_request_limit;
            $monthlyLimit = $locked->monthly_request_limit === null ? null : (int) $locked->monthly_request_limit;
            $dailyUsed = (int) DB::table('api_client_usage')->where('client_id', $client->id)->where('usage_date', $today)->value('requests');
            $monthlyUsed = (int) DB::table('api_client_usage')->where('client_id', $client->id)
                ->whereBetween('usage_date', [$monthStart, $today])->sum('requests');

            $dailyExceeded = $dailyLimit !== null && $dailyUsed >= $dailyLimit;
            $monthlyExceeded = $monthlyLimit !== null && $monthlyUsed >= $monthlyLimit;
            if ($dailyExceeded || $monthlyExceeded) {
                $resetTimes = [];
                if ($dailyExceeded) $resetTimes[] = $now->addDay()->startOfDay();
                if ($monthlyExceeded) $resetTimes[] = $now->addMonthNoOverflow()->startOfMonth();
                $reset = collect($resetTimes)->sortByDesc(fn (CarbonImmutable $time) => $time->getTimestamp())->first();
                $headers = $this->quotaHeaders($dailyLimit, $monthlyLimit, $dailyUsed, $monthlyUsed, $now);
                $headers['Retry-After'] = (string) max(1, $reset->getTimestamp() - $now->getTimestamp());
                return ['allowed' => false, 'headers' => $headers];
            }

            $this->incrementDaily($client->id, $today);
            return [
                'allowed' => true,
                'headers' => $this->quotaHeaders($dailyLimit, $monthlyLimit, $dailyUsed + 1, $monthlyUsed + 1, $now),
            ];
        }, 3);
    }

    public function snapshot(object $client): array
    {
        $now = CarbonImmutable::now('UTC');
        $today = $now->toDateString();
        $monthStart = $now->startOfMonth()->toDateString();
        $dailyUsed = (int) DB::table('api_client_usage')->where('client_id', $client->id)->where('usage_date', $today)->value('requests');
        $monthlyUsed = (int) DB::table('api_client_usage')->where('client_id', $client->id)
            ->whereBetween('usage_date', [$monthStart, $today])->sum('requests');
        $dailyLimit = $client->daily_request_limit === null ? null : (int) $client->daily_request_limit;
        $monthlyLimit = $client->monthly_request_limit === null ? null : (int) $client->monthly_request_limit;

        return [
            'period' => 'UTC calendar day and calendar month',
            'requests_today' => $dailyUsed,
            'requests_this_month' => $monthlyUsed,
            'daily_limit' => $dailyLimit,
            'daily_remaining' => $dailyLimit === null ? null : max(0, $dailyLimit - $dailyUsed),
            'daily_resets_at' => $now->addDay()->startOfDay()->toIso8601String(),
            'monthly_limit' => $monthlyLimit,
            'monthly_remaining' => $monthlyLimit === null ? null : max(0, $monthlyLimit - $monthlyUsed),
            'monthly_resets_at' => $now->addMonthNoOverflow()->startOfMonth()->toIso8601String(),
        ];
    }

    public function reject(Request $request, array $quota): JsonResponse
    {
        return response()->json([
            'type' => 'about:blank', 'title' => 'API request quota exceeded', 'status' => 429,
            'error' => 'quota_exceeded', 'request_id' => $request->attributes->get('request_id'),
        ], 429, ['Content-Type' => 'application/problem+json', 'Cache-Control' => 'no-store'] + $quota['headers']);
    }

    public function attach(Response $response, array $quota): Response
    {
        foreach ($quota['headers'] as $name => $value) $response->headers->set($name, $value);
        return $response;
    }

    private function incrementDaily(string $clientId, string $date): void
    {
        DB::table('api_client_usage')->insertOrIgnore(['client_id' => $clientId, 'usage_date' => $date, 'requests' => 0]);
        DB::table('api_client_usage')->where('client_id', $clientId)->where('usage_date', $date)->increment('requests');
    }

    private function quotaHeaders(?int $dailyLimit, ?int $monthlyLimit, int $dailyUsed, int $monthlyUsed, CarbonImmutable $now): array
    {
        $headers = [];
        if ($dailyLimit !== null) {
            $headers['X-Quota-Daily-Limit'] = (string) $dailyLimit;
            $headers['X-Quota-Daily-Remaining'] = (string) max(0, $dailyLimit - $dailyUsed);
            $headers['X-Quota-Daily-Reset'] = (string) $now->addDay()->startOfDay()->getTimestamp();
        }
        if ($monthlyLimit !== null) {
            $headers['X-Quota-Monthly-Limit'] = (string) $monthlyLimit;
            $headers['X-Quota-Monthly-Remaining'] = (string) max(0, $monthlyLimit - $monthlyUsed);
            $headers['X-Quota-Monthly-Reset'] = (string) $now->addMonthNoOverflow()->startOfMonth()->getTimestamp();
        }
        return $headers;
    }
}
