<?php

namespace App\Http\Controllers\Api\V1;

use App\Contexts\ApiIdentity\Application\Services\MachineClientQuota;
use Illuminate\Http\Request;

final class IntegrationQuotaController
{
    public function __invoke(Request $request, MachineClientQuota $quota)
    {
        return response()->json(['data' => $quota->snapshot($request->attributes->get('api_client'))]);
    }
}
