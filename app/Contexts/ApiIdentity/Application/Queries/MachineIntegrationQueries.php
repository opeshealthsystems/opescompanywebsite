<?php

namespace App\Contexts\ApiIdentity\Application\Queries;

use App\Contexts\Shared\Application\WorkflowResult;
use Illuminate\Support\Facades\DB;

/** Machine-client identity and quota query behind the client-credentials API adapter. */
final class MachineIntegrationQueries
{
    public function execute(object $client, array $scopes): WorkflowResult
    {
        return WorkflowResult::payload([
            'client_id' => $client->id,
            'name' => $client->name,
            'scopes' => $scopes,
            'requests_today' => (int) DB::table('api_client_usage')->where('client_id', $client->id)
                ->where('usage_date', now()->toDateString())->value('requests'),
        ]);
    }
}
