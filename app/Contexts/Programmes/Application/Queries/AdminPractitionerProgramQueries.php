<?php

namespace App\Contexts\Programmes\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminPractitionerProgramQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return PractitionerProgram::query()->withCount(['applications', 'cohorts'])->orderByDesc('id');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['type' => 'sometimes|in:volunteer,paid', 'program_type' => 'sometimes|in:general,validation', 'status' => 'sometimes|in:draft,open,closed,archived', 'search' => 'sometimes|nullable|string|max:150']);
        $query = $this->query($input);
        foreach (['type', 'program_type', 'status'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        if (!empty($filters['search'])) $query->where(fn ($q) => $q->where('title', 'like', '%'.trim($filters['search']).'%')->orWhere('title_fr', 'like', '%'.trim($filters['search']).'%'));
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->items(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, PractitionerProgram $program): WorkflowResult
    {
        $this->admin($input); $program->loadCount(['applications', 'cohorts']);
        return WorkflowResult::payload(['program' => $program]);
    }
}
