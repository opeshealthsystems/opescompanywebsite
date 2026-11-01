<?php

namespace App\Contexts\Operations\Application\Commands;

use App\Contexts\Shared\Application\{WorkflowInput, WorkflowResult};
use App\Models\Project;

final class ProjectCommands
{
    public function create(WorkflowInput $input): WorkflowResult
    {
        $data = $this->values($input);
        $project = Project::create([...$data, 'reference' => Project::generateReference()]);
        return WorkflowResult::payload(['record' => $project], 201);
    }

    public function update(WorkflowInput $input, Project $project): WorkflowResult
    {
        $project->update($this->values($input));
        return WorkflowResult::payload(['record' => $project]);
    }

    public function delete(WorkflowInput $input, Project $project): WorkflowResult
    {
        $id = $project->id; $project->delete();
        return WorkflowResult::payload(['deleted_id' => $id]);
    }

    private function values(WorkflowInput $input): array
    {
        return $input->validate(['title' => 'required|string|max:250', 'description' => 'nullable|string|max:20000',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled', 'priority' => 'required|in:low,medium,high,critical',
            'owner_id' => 'required|exists:users,id', 'start_date' => 'nullable|date', 'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0', 'currency' => 'required|in:XAF,USD,EUR', 'notes' => 'nullable|string|max:20000']);
    }
}
