<?php

namespace App\Contexts\ApiIdentity\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\User;
use Laravel\Passport\Passport;

final class AdminOAuthClientQueries extends WorkflowHandler
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
            ->where('grant_types', 'like', '%authorization_code%')
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
                'redirect_uris' => array_values($client->redirect_uris ?? []),
                'scopes' => array_values(array_intersect($client->scopes ?? [], array_keys(\App\Contexts\ApiIdentity\Application\BusinessScopes::definitions()))),
                'revoked' => (bool) $client->revoked,
                'created_at' => $client->created_at,
            ];
        })->all();

        return WorkflowResult::payload(['items' => $items, 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(),
            'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }
}
