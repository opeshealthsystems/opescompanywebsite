<?php

namespace App\Contexts\ApiIdentity\Application\Queries;

use App\Contexts\ApiIdentity\Application\BusinessScopes;
use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\User;
use Laravel\Passport\Passport;

/** Account-scoped public OAuth application projections for the developer portal. */
final class DeveloperOAuthApplicationQueries extends WorkflowHandler
{
    public function index(WorkflowInput $input): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active, 403);

        $page = $input->paginate(Passport::client()->newQuery()
            ->where('owner_type', $actor->getMorphClass())
            ->where('owner_id', $actor->getKey())
            ->where('grant_types', 'like', '%authorization_code%')
            ->orderByDesc('created_at'), 20);
        $allowedScopes = BusinessScopes::allowed($actor);

        $items = $page->getCollection()->map(fn ($client): array => [
            'client_id' => (string) $client->getKey(),
            'name' => $client->name,
            'redirect_uris' => array_values($client->redirect_uris ?? []),
            'scopes' => array_values(array_intersect($client->scopes ?? [], $allowedScopes)),
            'revoked' => (bool) $client->revoked,
            'created_at' => $client->created_at?->toIso8601String(),
        ])->all();

        return WorkflowResult::payload(['items' => $items, 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(),
            'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }
}
