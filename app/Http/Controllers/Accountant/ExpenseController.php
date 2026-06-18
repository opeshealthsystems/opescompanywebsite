<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::with('submitter')
            ->when($request->status,   fn ($q) => $q->where('status', $request->status))
            ->when($request->category, fn ($q) => $q->where('category', $request->category))
            ->when($request->from,     fn ($q) => $q->where('expense_date', '>=', $request->from))
            ->when($request->to,       fn ($q) => $q->where('expense_date', '<=', $request->to))
            ->orderByDesc('expense_date')
            ->paginate(25)
            ->withQueryString();

        return view('accountant.expenses.index', compact('expenses'));
    }

    public function approve($id)
    {
        Expense::findOrFail($id)->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Expense approved.');
    }

    public function reject($id)
    {
        Expense::findOrFail($id)->update(['status' => 'rejected']);

        return back()->with('success', 'Expense rejected.');
    }
}
