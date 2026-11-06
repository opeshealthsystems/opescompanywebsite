<?php

namespace App\Contexts\ApiIdentity\Application\Queries;

use App\Contexts\ApiIdentity\Application\Services\MachineClientQuota;
use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\User;
use Laravel\Passport\Passport;

/** Account-scoped machine-client metadata; credential material is never projected. */
final class DeveloperMachineClientQueries extends WorkflowHandler
{
    private const ALLOWED_SCOPES = ['catalogue:read', 'integration:read'];

    public function index(WorkflowInput $input): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active, 403);

        $page = $input->paginate(Passport::client()->newQuery()
            ->where('owner_type', $actor->getMorphClass())
            ->where('owner_id', $actor->getKey())
            ->where('grant_types', 'like', '%client_credentials%')
            ->orderByDesc('created_at'), 20);

        $items = $page->getCollection()->map(function ($client): array {
            $usage = app(MachineIntegrationUsageQueries::class)->execute($client)->data;
            $quota = app(MachineClientQuota::class)->snapshot($client);
            return [
                'client_id' => (string) $client->getKey(),
                'name' => $client->name,
                'scopes' => array_values(array_intersect($client->scopes ?? [], self::ALLOWED_SCOPES)),
                'revoked' => (bool) $client->revoked,
                'created_at' => $client->created_at?->toIso8601String(),
                'usage' => $usage,
                'quota' => $quota,
            ];
        })->all();

        return WorkflowResult::payload(['items' => $items, 'pagination' => [
            'current_page' => $page->currentPage(),
            'per_page' => $page->perPage(),
            'total' => $page->total(),
            'last_page' => $page->lastPage(),
        ]]);
    }
}
