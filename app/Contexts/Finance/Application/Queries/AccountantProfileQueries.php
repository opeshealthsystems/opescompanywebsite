<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Models\AccountantProfile;
class AccountantProfileQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function show(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = $request->user()->load('accountantProfile');
        return \App\Contexts\Shared\Application\WorkflowResult::read('accountant_profilecontroller_show_1', compact('user'));
    }
}
