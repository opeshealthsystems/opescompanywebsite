<?php

namespace App\Http\Controllers\Api\V1;

use App\Contexts\ApiIdentity\Application\Queries\MachineIntegrationUsageQueries;
use Illuminate\Http\Request;

final class IntegrationUsageController
{
    public function __invoke(Request $request, MachineIntegrationUsageQueries $query)
    {
        $client = $request->attributes->get('api_client');
        $result = $query->execute($client);

        return response()->json(['data' => $result->data]);
    }
}
