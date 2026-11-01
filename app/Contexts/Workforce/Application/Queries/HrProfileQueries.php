<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\EmployeeProfile;
class HrProfileQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function show(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = $request->user()->load('employeeProfile');
        return \App\Contexts\Shared\Application\WorkflowResult::read('hr_profilecontroller_show_1', compact('user'));
    }
}
