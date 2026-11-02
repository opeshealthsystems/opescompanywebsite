<?php

namespace App\Contexts\ApiIdentity\Application\Queries;

use App\Contexts\Shared\Application\WorkflowResult;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/** Client-scoped UTC usage projection for approved machine integrations. */
final class MachineIntegrationUsageQueries
{
    public function execute(object $client): WorkflowResult
    {
        $today = CarbonImmutable::now('UTC')->startOfDay();
        $start = $today->subDays(29);
        $counts = DB::table('api_client_usage')
            ->where('client_id', $client->id)
            ->whereBetween('usage_date', [$start->toDateString(), $today->toDateString()])
            ->orderBy('usage_date')
            ->pluck('requests', 'usage_date');

        $dailyUsage = [];
        for ($day = $start; $day <= $today; $day = $day->addDay()) {
            $date = $day->toDateString();
            $dailyUsage[] = ['date' => $date, 'requests' => (int) ($counts[$date] ?? 0)];
        }

        return WorkflowResult::payload([
            'period_start' => $start->toDateString(),
            'period_end' => $today->toDateString(),
            'requests_today' => (int) ($counts[$today->toDateString()] ?? 0),
            'requests_last_30_days' => (int) $counts->sum(),
            'daily_usage' => $dailyUsage,
        ]);
    }
}
