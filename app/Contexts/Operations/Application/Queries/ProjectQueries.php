<?php

namespace App\Contexts\Operations\Application\Queries;

use App\Contexts\Shared\Application\{WorkflowInput, WorkflowResult};
use App\Models\Project;
use App\Models\Milestone;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class ProjectQueries
{
    public function index(WorkflowInput $input): WorkflowResult
    {
        $filters = $input->validate([
            'status' => 'sometimes|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'sometimes|in:low,medium,high,critical',
            'search' => 'sometimes|nullable|string|max:150',
        ]);
        $query = $this->query($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['priority'])) $query->where('priority', $filters['priority']);
        if (!empty($filters['search'])) $query->where('title', 'like', '%'.trim($filters['search']).'%');

        return WorkflowResult::payload(['projects' => $input->paginate($query)]);
    }

    public function query(WorkflowInput $input): Builder
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);

        return Project::query()->with('owner')->withCount('milestones')->latest();
    }

    public function show(WorkflowInput $input, Project $project): WorkflowResult
    {
        return WorkflowResult::payload(['project' => $project->load('owner', 'milestones')]);
    }

    public function milestones(WorkflowInput $input, Project $project): WorkflowResult
    {
        $page = $input->paginate($this->milestoneQuery($input, $project));
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (Milestone $milestone) => $milestone->only(['id', 'project_id', 'title', 'description', 'due_date', 'status', 'completed_at']))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function milestoneQuery(WorkflowInput $input, Project $project): Builder
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);

        return $project->milestones()->getQuery()->orderBy('due_date')->orderBy('id');
    }

    public function milestone(WorkflowInput $input, Project $project, Milestone $milestone): WorkflowResult
    {
        $actor = $input->user(); abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        abort_unless((int) $milestone->project_id === (int) $project->id, 404);
        return WorkflowResult::payload(['milestone' => $milestone->only(['id', 'project_id', 'title', 'description', 'due_date', 'status', 'completed_at'])]);
    }
}
