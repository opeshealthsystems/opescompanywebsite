<?php

namespace App\Contexts\CustomerServices\Application\Queries;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
class CustomerProfileQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $profile = $user->customerProfile ?? $user->customerProfile()->make(['country' => 'CM']);
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_profilecontroller_show_1', compact('user', 'profile'));
    }
}
