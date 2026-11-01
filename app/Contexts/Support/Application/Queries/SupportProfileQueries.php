<?php

namespace App\Contexts\Support\Application\Queries;

use App\Models\SupportProfile;
class SupportProfileQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function show(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = $request->user()->load('supportProfile');
        return \App\Contexts\Shared\Application\WorkflowResult::read('support_profilecontroller_show_1', compact('user'));
    }
}
