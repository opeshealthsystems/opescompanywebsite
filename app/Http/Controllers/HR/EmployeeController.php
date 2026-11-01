<?php
namespace App\Http\Controllers\HR;

/** Web presentation adapter; business queries and commands live in application contexts. */
class EmployeeController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('hr.employeecontroller.index', $request); }
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('hr.employeecontroller.show', $request); }
}
