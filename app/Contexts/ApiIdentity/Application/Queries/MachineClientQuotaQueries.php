<?php

namespace App\Contexts\ApiIdentity\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\ApiIdentity\Application\Services\MachineClientQuota;
use App\Models\User;
use Laravel\Passport\Passport;

final class MachineClientQuotaQueries extends WorkflowHandler
{
    public function current(object $client): WorkflowResult
    {
        return WorkflowResult::payload(app(MachineClientQuota::class)->snapshot($client));
    }

    public function admin(WorkflowInput $input, string $clientId): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        $client = Passport::client()->newQuery()->whereKey($clientId)
            ->where('grant_types', 'like', '%client_credentials%')->firstOrFail();

        return WorkflowResult::payload([
            'client' => ['client_id' => (string) $client->getKey(), 'name' => $client->name, 'revoked' => (bool) $client->revoked],
            'quota' => app(MachineClientQuota::class)->snapshot($client),
        ]);
    }
}
