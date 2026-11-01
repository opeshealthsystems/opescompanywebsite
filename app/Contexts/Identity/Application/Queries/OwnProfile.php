<?php

namespace App\Contexts\Identity\Application\Queries;

use App\Contexts\ApiIdentity\Application\BusinessScopes;
use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;

/** Minimal self profile for authenticated clients to select the right workspace. */
final class OwnProfile extends WorkflowHandler
{
    public function execute(WorkflowInput $input): WorkflowResult
    {
        $user = $input->user();
        abort_unless($user?->is_active && in_array('identity:read', $input->scopes, true), 403);

        return WorkflowResult::payload([
            'id' => (string) $user->getKey(),
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'roles' => $user->getRoleNames()->values()->all(),
            'scopes' => array_values(array_intersect(BusinessScopes::allowed($user), $input->scopes)),
        ]);
    }
}
