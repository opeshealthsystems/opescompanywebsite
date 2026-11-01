<?php

namespace App\Contexts\Operations\Application\Queries;

use App\Contexts\Shared\Application\{WorkflowInput, WorkflowResult};
use App\Models\Project;

final class ProjectQueries
{
    public function index(WorkflowInput $input): WorkflowResult
    {
        $input->validate(['status' => 'sometimes|in:planning,active,on_hold,completed,cancelled']);
        return WorkflowResult::payload(['projects' => $input->paginate(Project::with('owner')->when($input->status, fn ($q) => $q->where('status', $input->status))->latest())]);
    }

    public function show(WorkflowInput $input, Project $project): WorkflowResult
    {
        return WorkflowResult::payload(['project' => $project->load('owner', 'milestones')]);
    }
}
