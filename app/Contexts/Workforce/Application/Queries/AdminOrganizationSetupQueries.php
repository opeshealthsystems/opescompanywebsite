<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Department;
use App\Models\LeaveType;
use App\Models\User;

/** Admin-scoped reference-data queries for workforce API consumers. */
final class AdminOrganizationSetupQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    public function departments(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $page = $input->paginate(Department::query()->with('parent:id,name')->orderBy('name'));
        $items = $page->getCollection()->map(fn (Department $record) => ['id' => $record->id, 'name' => $record->name, 'code' => $record->code,
            'head_id' => $record->head_id, 'parent_id' => $record->parent_id, 'parent_name' => $record->parent?->name,
            'is_active' => (bool) $record->is_active, 'description' => $record->description])->all();
        return WorkflowResult::payload(['items' => $items, 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function department(WorkflowInput $input, Department $department): WorkflowResult
    {
        $this->admin($input);
        $department->load('parent:id,name');
        return WorkflowResult::payload(['department' => ['id' => $department->id, 'name' => $department->name, 'code' => $department->code,
            'head_id' => $department->head_id, 'parent_id' => $department->parent_id, 'parent_name' => $department->parent?->name,
            'is_active' => (bool) $department->is_active, 'description' => $department->description]]);
    }

    public function leaveTypes(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $page = $input->paginate(LeaveType::query()->orderBy('name'));
        $items = $page->getCollection()->map(fn (LeaveType $record) => ['id' => $record->id, 'name' => $record->name, 'code' => $record->code,
            'days_per_year' => $record->days_per_year, 'max_carry_forward' => $record->max_carry_forward, 'color' => $record->color,
            'is_paid' => (bool) $record->is_paid, 'requires_approval' => (bool) $record->requires_approval, 'is_active' => (bool) $record->is_active])->all();
        return WorkflowResult::payload(['items' => $items, 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function leaveType(WorkflowInput $input, LeaveType $leaveType): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['leave_type' => ['id' => $leaveType->id, 'name' => $leaveType->name, 'code' => $leaveType->code,
            'days_per_year' => $leaveType->days_per_year, 'max_carry_forward' => $leaveType->max_carry_forward, 'color' => $leaveType->color,
            'is_paid' => (bool) $leaveType->is_paid, 'requires_approval' => (bool) $leaveType->requires_approval,
            'is_active' => (bool) $leaveType->is_active]]);
    }
}
