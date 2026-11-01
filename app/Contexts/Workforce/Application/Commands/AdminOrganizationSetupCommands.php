<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Department;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/** Shared API and Filament commands for workforce reference data. */
final class AdminOrganizationSetupCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function departmentValues(WorkflowInput $input, ?Department $department = null): array
    {
        $id = $department?->id;
        return $input->validate([
            'name' => ($id ? 'sometimes|' : '').'required|string|max:150',
            'code' => ['sometimes', 'nullable', 'string', 'max:20', Rule::unique('departments', 'code')->ignore($id)],
            'head_id' => 'sometimes|nullable|integer|exists:users,id',
            'parent_id' => ['sometimes', 'nullable', 'integer', Rule::exists('departments', 'id')->where('is_active', true)],
            'is_active' => ($id ? 'sometimes|' : '').'required|boolean',
            'description' => 'sometimes|nullable|string|max:5000',
            'id' => 'prohibited', 'members_count' => 'prohibited',
        ]);
    }

    private function validateDepartmentParent(array $data, ?Department $department = null): void
    {
        $parentId = $data['parent_id'] ?? null;
        if (!$parentId) return;
        if ($department && (int) $parentId === (int) $department->id) throw ValidationException::withMessages(['parent_id' => 'A department cannot be its own parent.']);
        $visited = [];
        $cursor = Department::query()->findOrFail($parentId);
        while ($cursor && !isset($visited[$cursor->id])) {
            if ($department && (int) $cursor->id === (int) $department->id) throw ValidationException::withMessages(['parent_id' => 'A department cannot be placed beneath one of its descendants.']);
            $visited[$cursor->id] = true;
            $cursor = $cursor->parent_id ? Department::query()->find($cursor->parent_id) : null;
        }
    }

    public function createDepartment(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->departmentValues($input);
        $this->validateDepartmentParent($data);
        $record = DB::transaction(function () use ($data, $actor) {
            $record = Department::create($data);
            DomainOutbox::append('event', 'workforce.department_created.v1', ['references' => [['resource' => 'Department', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function updateDepartment(WorkflowInput $input, Department $department): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->departmentValues($input, $department);
        if ($data === []) throw ValidationException::withMessages(['department' => 'Provide at least one department field to update.']);
        $record = DB::transaction(function () use ($department, $data, $actor) {
            $record = Department::query()->lockForUpdate()->findOrFail($department->id);
            $this->validateDepartmentParent($data + ['parent_id' => $record->parent_id], $record);
            $record->update($data);
            DomainOutbox::append('event', 'workforce.department_updated.v1', ['references' => [['resource' => 'Department', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function deleteDepartment(WorkflowInput $input, Department $department): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($department, $actor) {
            $record = Department::query()->lockForUpdate()->findOrFail($department->id);
            if ($record->members()->exists() || $record->children()->exists()) throw ValidationException::withMessages(['department' => 'Departments with members or child departments cannot be deleted.']);
            $id = $record->id; $record->delete();
            DomainOutbox::append('event', 'workforce.department_deleted.v1', ['references' => [['resource' => 'Department', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }

    private function leaveTypeValues(WorkflowInput $input, ?LeaveType $leaveType = null): array
    {
        $id = $leaveType?->id;
        return $input->validate([
            'name' => ($id ? 'sometimes|' : '').'required|string|max:100',
            'code' => ['sometimes', $id ? 'sometimes' : 'required', 'string', 'max:30', Rule::unique('leave_types', 'code')->ignore($id)],
            'days_per_year' => ($id ? 'sometimes|' : '').'required|integer|min:0|max:366',
            'max_carry_forward' => 'sometimes|nullable|integer|min:0|max:366',
            'color' => 'sometimes|nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_paid' => ($id ? 'sometimes|' : '').'required|boolean',
            'requires_approval' => ($id ? 'sometimes|' : '').'required|boolean',
            'is_active' => ($id ? 'sometimes|' : '').'required|boolean',
            'id' => 'prohibited',
        ]);
    }

    public function createLeaveType(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->leaveTypeValues($input);
        $record = DB::transaction(function () use ($data, $actor) {
            $record = LeaveType::create($data);
            DomainOutbox::append('event', 'workforce.leave_type_created.v1', ['references' => [['resource' => 'LeaveType', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function updateLeaveType(WorkflowInput $input, LeaveType $leaveType): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->leaveTypeValues($input, $leaveType);
        if ($data === []) throw ValidationException::withMessages(['leave_type' => 'Provide at least one leave type field to update.']);
        $record = DB::transaction(function () use ($leaveType, $data, $actor) {
            $record = LeaveType::query()->lockForUpdate()->findOrFail($leaveType->id);
            $record->update($data);
            DomainOutbox::append('event', 'workforce.leave_type_updated.v1', ['references' => [['resource' => 'LeaveType', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function deleteLeaveType(WorkflowInput $input, LeaveType $leaveType): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($leaveType, $actor) {
            $record = LeaveType::query()->lockForUpdate()->findOrFail($leaveType->id);
            if ($record->leaveRequests()->exists()) throw ValidationException::withMessages(['leave_type' => 'Leave types with request history cannot be deleted.']);
            $id = $record->id; $record->delete();
            DomainOutbox::append('event', 'workforce.leave_type_deleted.v1', ['references' => [['resource' => 'LeaveType', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
