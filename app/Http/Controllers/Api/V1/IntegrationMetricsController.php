<?php

namespace App\Http\Controllers\Api\V1;

use App\Contexts\ApiIdentity\Application\Queries\ApiRequestMetricsQueries;
use Illuminate\Http\Request;

final class IntegrationMetricsController
{
    public function __invoke(Request $request, ApiRequestMetricsQueries $metrics)
    {
        $client = $request->attributes->get('api_client');

        return response()->json(['data' => $metrics->execute((string) $client->id, 'machine', (string) $client->id)]);
    }
}
