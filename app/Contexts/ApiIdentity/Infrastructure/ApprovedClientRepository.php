<?php

namespace App\Contexts\ApiIdentity\Infrastructure;

use App\Models\User;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

class ApprovedClientRepository extends ClientRepository
{
    public function find(string|int $id): ?Client
    {
        if (!\Illuminate\Support\Str::isUuid((string) $id)) return null;
        // Read fresh for revocation/rotation, including long-lived application workers.
        return \Laravel\Passport\Passport::client()->newQuery()->find($id);
    }

    public function findActive(string|int $id): ?Client
    {
        $client = parent::findActive($id);
        if (!$client || !$client->owner instanceof User || !$client->owner->is_active || !is_array($client->scopes)) return null;
        return $client;
    }
}
