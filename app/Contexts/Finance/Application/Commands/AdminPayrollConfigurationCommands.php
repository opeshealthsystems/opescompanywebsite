<?php

namespace App\Contexts\Finance\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\PayrollDeductionType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AdminPayrollConfigurationCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user(); abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403); return $actor;
    }

    private function values(WorkflowInput $input, ?PayrollDeductionType $type = null): array
    {
        $id = $type?->id;
        return $input->validate([
            'name' => ($id ? 'sometimes|' : '').'required|string|max:100',
            'code' => [$id ? 'sometimes' : 'required', 'string', 'max:30', Rule::unique('payroll_deduction_types', 'code')->ignore($id)],
            'calculation_type' => ($id ? 'sometimes|' : '').'required|in:percentage,fixed',
            'rate' => ($id ? 'sometimes|' : '').'required|numeric|min:0',
            'apply_by_default' => ($id ? 'sometimes|' : '').'required|boolean', 'is_active' => ($id ? 'sometimes|' : '').'required|boolean',
            'sort_order' => ($id ? 'sometimes|' : '').'required|integer|min:0|max:9999', 'description' => 'sometimes|nullable|string|max:5000', 'id' => 'prohibited',
        ]);
    }

    private function validateRate(array $data, ?PayrollDeductionType $record = null): void
    {
        $calculation = $data['calculation_type'] ?? $record?->calculation_type;
        $rate = $data['rate'] ?? $record?->rate;
        if ($calculation === 'percentage' && (float) $rate > 100) throw ValidationException::withMessages(['rate' => 'Percentage deductions cannot exceed 100%.']);
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input); $this->validateRate($data);
        $record = DB::transaction(function () use ($data, $actor) { $record = PayrollDeductionType::create($data); DomainOutbox::append('event', 'finance.payroll_deduction_type_created.v1', ['references' => [['resource' => 'PayrollDeductionType', 'id' => $record->id]]], $actor->id); return $record; }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function update(WorkflowInput $input, PayrollDeductionType $deductionType): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input, $deductionType);
        if (!$data) throw ValidationException::withMessages(['deduction_type' => 'Provide at least one field to update.']);
        $record = DB::transaction(function () use ($deductionType, $data, $actor) { $record = PayrollDeductionType::query()->lockForUpdate()->findOrFail($deductionType->id); $this->validateRate($data, $record); $record->update($data);
            DomainOutbox::append('event', 'finance.payroll_deduction_type_updated.v1', ['references' => [['resource' => 'PayrollDeductionType', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id); return $record; }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function delete(WorkflowInput $input, PayrollDeductionType $deductionType): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($deductionType, $actor) { $record = PayrollDeductionType::query()->lockForUpdate()->findOrFail($deductionType->id);
            if ($record->is_active || $record->apply_by_default) throw ValidationException::withMessages(['deduction_type' => 'Deactivate the deduction and remove it from default payroll runs before deleting it.']);
            $id = $record->id; $record->delete(); DomainOutbox::append('event', 'finance.payroll_deduction_type_deleted.v1', ['references' => [['resource' => 'PayrollDeductionType', 'id' => $id]]], $actor->id); return $id; }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
