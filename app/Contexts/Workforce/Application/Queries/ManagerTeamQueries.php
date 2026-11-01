<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\Department;
use App\Models\User;
class ManagerTeamQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $dept = \App\Models\Department::where('head_id', $user->id)->first();
        $query = \App\Models\User::query()->when($dept, fn($q) => $q->where('department_id', $dept->id), fn($emptyScope) => $emptyScope->whereRaw('1 = 0'))->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        $team = $request->paginate($query, 20);
        return \App\Contexts\Shared\Application\WorkflowResult::read('manager_teamcontroller_index_1', compact('team', 'dept'));
    }
}
