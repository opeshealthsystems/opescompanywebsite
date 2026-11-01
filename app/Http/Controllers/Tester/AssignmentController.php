<?php
namespace App\Http\Controllers\Tester;

/** Web presentation adapter; business queries and commands live in application contexts. */
class AssignmentController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('tester.assignmentcontroller.index', $request); }
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('tester.assignmentcontroller.show', $request); }
    public function storeBugReport(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('tester.assignmentcontroller.storeBugReport', $request); }
    public function updateStatus(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('tester.assignmentcontroller.updateStatus', $request); }
}
