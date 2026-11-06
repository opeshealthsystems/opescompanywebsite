<?php

namespace App\Http\Controllers\Api\V1;

use App\Contexts\ApiIdentity\Application\Queries\DelegatedBusinessUsageQueries;
use Illuminate\Http\Request;

final class DelegatedBusinessUsageController
{
    public function __invoke(Request $request, DelegatedBusinessUsageQueries $query)
    {
        abort_unless(in_array('identity:read', $request->attributes->get('api_scopes', []), true), 403);

        return response()->json(['data' => $query->execute(
            $request->attributes->get('api_client'),
            (int) $request->user()->getAuthIdentifier(),
        )->data]);
    }
}
