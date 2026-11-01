<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Models\Expense;
class AccountantExpenseQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $expenses = $request->paginate(\App\Models\Expense::with('submitter')->when($request->status, fn($q) => $q->where('status', $request->status))->when($request->category, fn($q) => $q->where('category', $request->category))->when($request->from, fn($q) => $q->where('expense_date', '>=', $request->from))->when($request->to, fn($q) => $q->where('expense_date', '<=', $request->to))->orderByDesc('expense_date'), 25);
        return \App\Contexts\Shared\Application\WorkflowResult::read('accountant_expensecontroller_index_1', compact('expenses'));
    }
}
