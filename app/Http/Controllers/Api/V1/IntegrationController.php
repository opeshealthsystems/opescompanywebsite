<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Contexts\ApiIdentity\Application\Queries\MachineIntegrationQueries;

class IntegrationController
{
    public function __invoke(Request $request, MachineIntegrationQueries $query)
    {
        $client = $request->attributes->get('api_client');
        $result = $query->execute($client, $request->attributes->get('api_scopes', []));
        return response()->json(['data' => $result->data]);
    }
}
