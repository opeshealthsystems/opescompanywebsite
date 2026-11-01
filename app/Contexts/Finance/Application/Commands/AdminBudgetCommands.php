<?php

namespace App\Contexts\Finance\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Budget;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminBudgetCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); return $u; }
    private function values(WorkflowInput $input, bool $update = false): array
    {
        $p = $update ? 'sometimes|' : '';
        return $input->validate(['year' => $p.'required|integer|min:2000|max:2100', 'category' => $p.'required|in:payroll,rent,utilities,software,hardware,travel,marketing,legal,training,other',
            'department' => $p.'required|string|max:100', 'allocated_amount' => $p.'required|numeric|decimal:0,2|min:0|max:999999999999.99', 'currency' => $p.'required|in:XAF,USD,EUR', 'notes' => 'sometimes|nullable|string|max:5000', 'id' => 'prohibited']);
    }
    private function assertUnique(array $data, ?int $ignore = null): void
    {
        $q = Budget::query()->where('year', $data['year'])->where('category', $data['category'])->where('department', $data['department']);
        if ($ignore !== null) $q->where('id', '!=', $ignore);
        if ($q->exists()) throw ValidationException::withMessages(['department' => 'A budget already exists for this year, category and department.']);
    }
    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input);
        $r = DB::transaction(function () use ($data, $actor) { $this->assertUnique($data); $r = Budget::create($data); DomainOutbox::append('event', 'finance.budget_created.v1', ['references' => [['resource' => 'Budget', 'id' => $r->id]]], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $r, 'id' => $r->id], 201);
    }
    public function update(WorkflowInput $input, Budget $budget): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input, true); if (!$data) throw ValidationException::withMessages(['budget' => 'Provide at least one budget field to update.']);
        $r = DB::transaction(function () use ($budget, $data, $actor) { $r = Budget::query()->lockForUpdate()->findOrFail($budget->id); $this->assertUnique($data + ['year' => $r->year, 'category' => $r->category, 'department' => $r->department], $r->id); $r->update($data); DomainOutbox::append('event', 'finance.budget_updated.v1', ['references' => [['resource' => 'Budget', 'id' => $r->id]], 'changed_fields' => array_keys($data)], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $r, 'id' => $r->id]);
    }
    public function delete(WorkflowInput $input, Budget $budget): WorkflowResult
    {
        $actor = $this->admin($input); $id = DB::transaction(function () use ($budget, $actor) { $r = Budget::query()->lockForUpdate()->findOrFail($budget->id); $id = $r->id; $r->delete(); DomainOutbox::append('event', 'finance.budget_deleted.v1', ['references' => [['resource' => 'Budget', 'id' => $id]]], $actor->id); return $id; }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
