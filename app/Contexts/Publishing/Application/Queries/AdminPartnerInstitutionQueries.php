<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\PartnerInstitution;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminPartnerInstitutionQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return PartnerInstitution::query()->orderBy('sort_order')->orderBy('name');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['type' => 'sometimes|string|max:30', 'active' => 'sometimes|boolean', 'featured' => 'sometimes|boolean', 'search' => 'sometimes|nullable|string|max:150']);
        $query = $this->query($input);
        foreach (['type' => 'type', 'active' => 'is_active', 'featured' => 'is_featured'] as $filter => $column) if (isset($filters[$filter])) $query->where($column, $filters[$filter]);
        if (!empty($filters['search'])) $query->where(fn ($q) => $q->where('name', 'like', '%'.trim($filters['search']).'%')->orWhere('name_fr', 'like', '%'.trim($filters['search']).'%')->orWhere('city', 'like', '%'.trim($filters['search']).'%'));
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->items(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, PartnerInstitution $institution): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['institution' => $institution]);
    }
}
