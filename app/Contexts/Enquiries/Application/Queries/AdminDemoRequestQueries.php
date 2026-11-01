<?php

namespace App\Contexts\Enquiries\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\DemoRequest;

final class AdminDemoRequestQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function item(DemoRequest $request): array
    {
        return $request->only(['id', 'name', 'email', 'phone', 'organization_name', 'country', 'institution_type', 'institution_size',
            'products', 'message', 'preferred_date', 'locale', 'status', 'admin_notes', 'created_at', 'updated_at']);
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'status' => 'sometimes|in:'.implode(',', DemoRequest::$statuses),
            'institution_type' => 'sometimes|in:'.implode(',', array_keys(DemoRequest::$institutionTypes)),
            'search' => 'sometimes|nullable|string|max:150',
        ]);
        $query = DemoRequest::query()->orderByDesc('created_at');
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['institution_type'])) $query->where('institution_type', $filters['institution_type']);
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(fn ($builder) => $builder->where('name', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%')->orWhere('organization_name', 'like', '%'.$search.'%'));
        }
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (DemoRequest $request) => $this->item($request))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, DemoRequest $demoRequest): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['demo_request' => $this->item($demoRequest)]);
    }
}
