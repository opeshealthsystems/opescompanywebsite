<?php

namespace App\Http\Controllers;

use App\Models\PayrollEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function payslip(Request $request, PayrollEntry $entry)
    {
        abort_unless(
            $request->user()?->hasAnyRole(['super_admin', 'admin']),
            403
        );

        $run = $entry->payrollRun;

        $pdf = Pdf::loadView('payroll.payslip', compact('entry', 'run'))
            ->setPaper('a4', 'portrait');

        $filename = 'payslip-' . ($entry->employee?->employee_id ?? $entry->id) . '-' . $run->period_start->format('Y-m') . '.pdf';

        return $pdf->download($filename);
    }
}
