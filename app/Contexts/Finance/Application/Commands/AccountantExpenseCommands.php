<?php

namespace App\Contexts\Finance\Application\Commands;

use App\Contexts\Shared\Application\CurrentWorkflow;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class AccountantExpenseCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function approve(WorkflowInput $workflowInput, $id): \App\Contexts\Shared\Application\WorkflowResult
    {
        $actor = $workflowInput->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['accountant']), 403);
        $result = DB::transaction(function () use ($id, $actor) {
            $expense = Expense::query()->lockForUpdate()->findOrFail($id);
            if ($expense->status === 'pending') {
                $expense->update(['status' => 'approved', 'approved_by' => $actor->id, 'approved_at' => now()]);
                DomainOutbox::append('event', 'finance.expense_approved.v1', ['references' => [['resource' => 'Expense', 'id' => $expense->id]]], $actor->id);
            } elseif ($expense->status !== 'approved') throw ValidationException::withMessages(['status' => 'Only pending expenses can be approved.']);
            return ['id' => $expense->id, 'status' => $expense->status];
        }, 3);
        return \App\Contexts\Shared\Application\WorkflowResult::next('accountant_expensecontroller_approve_1', $workflowInput->api ? $result : [])->with('success', 'Expense approved.');
    }
    public function reject(WorkflowInput $workflowInput, $id): \App\Contexts\Shared\Application\WorkflowResult
    {
        $actor = $workflowInput->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['accountant']), 403);
        $result = DB::transaction(function () use ($id, $actor) {
            $expense = Expense::query()->lockForUpdate()->findOrFail($id);
            if ($expense->status === 'pending') {
                $expense->update(['status' => 'rejected']);
                DomainOutbox::append('event', 'finance.expense_rejected.v1', ['references' => [['resource' => 'Expense', 'id' => $expense->id]]], $actor->id);
            } elseif ($expense->status !== 'rejected') throw ValidationException::withMessages(['status' => 'Only pending expenses can be rejected.']);
            return ['id' => $expense->id, 'status' => $expense->status];
        }, 3);
        return \App\Contexts\Shared\Application\WorkflowResult::next('accountant_expensecontroller_reject_1', $workflowInput->api ? $result : [])->with('success', 'Expense rejected.');
    }
}
