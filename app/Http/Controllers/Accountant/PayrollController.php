<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\PayrollEntry;
use App\Models\PayrollRun;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index()
    {
        $runs = PayrollRun::withCount('entries')
            ->orderByDesc('period_start')
            ->paginate(20);

        return view('accountant.payroll.index', compact('runs'));
    }

    public function show(PayrollRun $run)
    {
        $run->load('processor');

        $byDept = PayrollEntry::where('payroll_run_id', $run->id)
            ->join('users', 'payroll_entries.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->selectRaw('departments.name as dept_name, SUM(payroll_entries.net_salary) as total_net, SUM(payroll_entries.gross_salary) as total_gross, COUNT(*) as count')
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('total_net')
            ->get();

        $entries = $run->entries()->with('employee')->paginate(30);

        return view('accountant.payroll.show', compact('run', 'byDept', 'entries'));
    }
}
