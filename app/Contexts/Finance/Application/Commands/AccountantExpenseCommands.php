<?php

namespace App\Contexts\Finance\Application\Commands;

use App\Models\Expense;
class AccountantExpenseCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function approve(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $id): \App\Contexts\Shared\Application\WorkflowResult
    {
        \App\Models\Expense::findOrFail($id)->update(['status' => 'approved', 'approved_by' => \App\Contexts\Shared\Application\CurrentWorkflow::id()]);
        return \App\Contexts\Shared\Application\WorkflowResult::next('accountant_expensecontroller_approve_1')->with('success', 'Expense approved.');
    }
    public function reject(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $id): \App\Contexts\Shared\Application\WorkflowResult
    {
        \App\Models\Expense::findOrFail($id)->update(['status' => 'rejected']);
        return \App\Contexts\Shared\Application\WorkflowResult::next('accountant_expensecontroller_reject_1')->with('success', 'Expense rejected.');
    }
}
