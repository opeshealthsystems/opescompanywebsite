<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\ManagerProfile;
class ManagerProfileQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function show(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = $request->user()->load('managerProfile');
        return \App\Contexts\Shared\Application\WorkflowResult::read('manager_profilecontroller_show_1', compact('user'));
    }
}
