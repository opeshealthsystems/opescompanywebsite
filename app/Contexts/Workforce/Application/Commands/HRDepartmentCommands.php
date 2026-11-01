<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Models\Department;
use App\Models\User;
class HRDepartmentCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function updateHead(\App\Contexts\Shared\Application\WorkflowInput $request, \App\Models\Department $dept): \App\Contexts\Shared\Application\WorkflowResult
    {
        $request->validate(['head_id' => 'required|exists:users,id']);
        $dept->update(['head_id' => $request->head_id]);
        return \App\Contexts\Shared\Application\WorkflowResult::next('hr_departmentcontroller_updateHead_1')->with('success', 'Department head updated.');
    }
}
