<?php

namespace App\Http\Controllers\Api\V1;

use App\Contexts\ApiIdentity\Application\Queries\ApiRequestMetricsQueries;
use Illuminate\Http\Request;

final class DelegatedBusinessMetricsController
{
    public function __invoke(Request $request, ApiRequestMetricsQueries $metrics)
    {
        abort_unless(in_array('identity:read', $request->attributes->get('api_scopes', []), true), 403);

        $client = $request->attributes->get('api_client');
        $user = $request->user();

        return response()->json(['data' => $metrics->execute((string) $client->id, 'user', (string) $user->getAuthIdentifier())]);
    }
}
