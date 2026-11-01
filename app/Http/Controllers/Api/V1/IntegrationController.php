<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntegrationController
{
    public function __invoke(Request $request)
    {
        $client = $request->attributes->get('api_client');
        return response()->json(['data' => [
            'client_id' => $client->id, 'name' => $client->name,
            'scopes' => $request->attributes->get('api_scopes'),
            'requests_today' => (int) DB::table('api_client_usage')->where('client_id', $client->id)->where('usage_date', now()->toDateString())->value('requests'),
        ]]);
    }
}
