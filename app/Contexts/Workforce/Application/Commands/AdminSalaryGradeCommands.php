<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\SalaryGrade;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AdminSalaryGradeCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user(); abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403); return $actor;
    }

    private function values(WorkflowInput $input, ?SalaryGrade $grade = null): array
    {
        $id = $grade?->id;
        return $input->validate([
            'name' => ($id ? 'sometimes|' : '').'required|string|max:100',
            'code' => [$id ? 'sometimes' : 'required', 'string', 'max:20', Rule::unique('salary_grades', 'code')->ignore($id)],
            'min_salary' => ($id ? 'sometimes|' : '').'required|numeric|min:0', 'max_salary' => ($id ? 'sometimes|' : '').'required|numeric|min:0',
            'currency' => ($id ? 'sometimes|' : '').'required|in:XAF,USD,EUR', 'is_active' => ($id ? 'sometimes|' : '').'required|boolean',
            'description' => 'sometimes|nullable|string|max:5000', 'id' => 'prohibited', 'employees_count' => 'prohibited',
        ]);
    }

    private function validateRange(array $data, ?SalaryGrade $grade = null): void
    {
        $min = $data['min_salary'] ?? $grade?->min_salary;
        $max = $data['max_salary'] ?? $grade?->max_salary;
        if ($min !== null && $max !== null && (float) $max < (float) $min) throw ValidationException::withMessages(['max_salary' => 'Maximum salary must be greater than or equal to minimum salary.']);
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input); $this->validateRange($data);
        $record = DB::transaction(function () use ($data, $actor) { $record = SalaryGrade::create($data); DomainOutbox::append('event', 'workforce.salary_grade_created.v1', ['references' => [['resource' => 'SalaryGrade', 'id' => $record->id]]], $actor->id); return $record; }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function update(WorkflowInput $input, SalaryGrade $salaryGrade): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input, $salaryGrade);
        if (!$data) throw ValidationException::withMessages(['salary_grade' => 'Provide at least one field to update.']);
        $record = DB::transaction(function () use ($salaryGrade, $data, $actor) {
            $record = SalaryGrade::query()->lockForUpdate()->findOrFail($salaryGrade->id); $this->validateRange($data, $record); $record->update($data);
            DomainOutbox::append('event', 'workforce.salary_grade_updated.v1', ['references' => [['resource' => 'SalaryGrade', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id); return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function delete(WorkflowInput $input, SalaryGrade $salaryGrade): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($salaryGrade, $actor) { $record = SalaryGrade::query()->lockForUpdate()->findOrFail($salaryGrade->id);
            if ($record->employees()->exists()) throw ValidationException::withMessages(['salary_grade' => 'Salary grades assigned to employees cannot be deleted.']);
            $id = $record->id; $record->delete(); DomainOutbox::append('event', 'workforce.salary_grade_deleted.v1', ['references' => [['resource' => 'SalaryGrade', 'id' => $id]]], $actor->id); return $id; }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
