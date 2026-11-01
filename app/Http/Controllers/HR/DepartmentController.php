<?php
namespace App\Http\Controllers\HR;

/** Web presentation adapter; business queries and commands live in application contexts. */
class DepartmentController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('hr.departmentcontroller.index', $request); }
    public function updateHead(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('hr.departmentcontroller.updateHead', $request); }
}
