<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\PayrollRun;

class PayrollController extends Controller
{
    public function index()
    {
        $runs = PayrollRun::withCount('entries')
            ->orderByDesc('period_start')
            ->paginate(20);

        return view('hr.payroll.index', compact('runs'));
    }

    public function show(PayrollRun $run)
    {
        $run->load(['entries.employee', 'processor']);
        $entries = $run->entries()->with('employee')->paginate(30);

        return view('hr.payroll.show', compact('run', 'entries'));
    }
}
