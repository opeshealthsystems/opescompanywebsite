<?php
namespace App\Http\Controllers;

/** Web presentation adapter; business queries and commands live in application contexts. */
class PayrollController extends \App\Http\Controllers\Controller
{
    public function payslip(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('payrollcontroller.payslip', $request); }
}
