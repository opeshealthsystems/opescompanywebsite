<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\PayrollEntry;
use Barryvdh\DomPDF\Facade\Pdf;
class PayrollQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function payslip(\App\Contexts\Shared\Application\WorkflowInput $request, \App\Models\PayrollEntry $entry): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($request->user()?->hasAnyRole(['super_admin', 'admin']), 403);
        $run = $entry->payrollRun;
        $pdf = \App\Contexts\Shared\Infrastructure\PdfRenderer::document('payroll.payslip', compact('entry', 'run'))->setPaper('a4', 'portrait');
        $filename = 'payslip-' . ($entry->employee?->employee_id ?? $entry->id) . '-' . $run->period_start->format('Y-m') . '.pdf';
        return \App\Contexts\Shared\Application\WorkflowResult::download($pdf->output(), $filename);
    }
}
