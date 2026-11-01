<?php

namespace App\Contexts\Support\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminServiceRequestQueries extends WorkflowHandler
{
    private function support(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin', 'support']), 403);
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->support($input);

        return ServiceRequest::query()->with(['customer:id,name,email', 'assignedTechnician:id,name'])->orderByDesc('id');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->support($input);
        $filters = $input->validate(['status' => 'sometimes|in:pending,confirmed,assigned,completed,cancelled', 'type' => 'sometimes|in:installation,maintenance,training,other', 'customer_id' => 'sometimes|integer|exists:users,id']);
        $query = $this->query($input);
        foreach (['status', 'type', 'customer_id'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        return $this->paginated($input, $query);
    }

    public function show(WorkflowInput $input, ServiceRequest $serviceRequest): WorkflowResult
    {
        $this->support($input); $serviceRequest->load(['customer:id,name,email', 'assignedTechnician:id,name']);
        return WorkflowResult::payload(['service_request' => $serviceRequest->only(['id', 'reference_number', 'customer_id', 'type', 'product_slug', 'description', 'preferred_date', 'preferred_time', 'location', 'status', 'assigned_technician_id', 'confirmed_date', 'confirmed_time', 'admin_notes', 'created_at', 'updated_at'])
            + ['customer' => $serviceRequest->customer?->only(['id', 'name', 'email']), 'assigned_technician' => $serviceRequest->assignedTechnician?->only(['id', 'name'])]]);
    }

    private function paginated(WorkflowInput $input, mixed $query): WorkflowResult
    {
        $page = $input->paginate($query);
        $items = $page->getCollection()->map(fn (ServiceRequest $record) => $record->only(['id', 'reference_number', 'customer_id', 'type', 'product_slug', 'preferred_date', 'preferred_time', 'location', 'status', 'assigned_technician_id', 'confirmed_date', 'confirmed_time', 'created_at'])
            + ['customer' => $record->customer?->only(['id', 'name', 'email']), 'assigned_technician' => $record->assignedTechnician?->only(['id', 'name'])])->all();
        return WorkflowResult::payload(['items' => $items, 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }
}
