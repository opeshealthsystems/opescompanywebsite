<?php

namespace App\Contexts\Testing\Application\Queries;

use App\Models\TesterProfile;
class TesterProfileQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function show(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = $request->user()->load('testerProfile');
        return \App\Contexts\Shared\Application\WorkflowResult::read('tester_profilecontroller_show_1', compact('user'));
    }
}
