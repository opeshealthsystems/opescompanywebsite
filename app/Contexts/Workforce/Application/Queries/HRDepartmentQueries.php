<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\Department;
use App\Models\User;
class HRDepartmentQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $departments = $workflowInput->collect(\App\Models\Department::with(['head', 'parent', 'children'])->withCount('members')->orderBy('name'));
        $employees = $workflowInput->collect(\App\Models\User::whereNotNull('employee_id')->orderBy('name'));
        return \App\Contexts\Shared\Application\WorkflowResult::read('hr_departmentcontroller_index_1', compact('departments', 'employees'));
    }
}
