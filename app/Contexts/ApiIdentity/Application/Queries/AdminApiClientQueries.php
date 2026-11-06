<?php

namespace App\Contexts\ApiIdentity\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;

final class AdminApiClientQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $query = Passport::client()->newQuery()
            ->where('grant_types', 'like', '%client_credentials%')
            ->with('owner')
            ->orderByDesc('created_at');
        $page = $input->paginate($query);

        $items = $page->getCollection()->map(function ($client): array {
            $owner = $client->owner;
            return [
                'client_id' => (string) $client->getKey(),
                'name' => $client->name,
                'owner_id' => $owner instanceof User ? $owner->id : null,
                'owner_name' => $owner instanceof User ? $owner->name : null,
                'scopes' => array_values(array_intersect($client->scopes ?? [], ['catalogue:read', 'integration:read'])),
                'revoked' => (bool) $client->revoked,
                'created_at' => $client->created_at,
            ];
        })->all();

        return WorkflowResult::payload(['items' => $items, 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(),
            'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }

    public function usage(WorkflowInput $input, string $clientId): WorkflowResult
    {
        $this->admin($input);
        $client = Passport::client()->newQuery()->whereKey($clientId)
            ->where('grant_types', 'like', '%client_credentials%')->firstOrFail();
        $usage = app(MachineIntegrationUsageQueries::class)->execute($client)->data;

        return WorkflowResult::payload([
            'client' => [
                'client_id' => (string) $client->getKey(),
                'name' => $client->name,
                'revoked' => (bool) $client->revoked,
                'scopes' => array_values(array_intersect($client->scopes ?? [], ['catalogue:read', 'integration:read'])),
            ],
            'usage' => $usage,
        ]);
    }
}
