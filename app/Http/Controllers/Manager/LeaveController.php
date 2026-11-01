<?php
namespace App\Http\Controllers\Manager;

/** Web presentation adapter; business queries and commands live in application contexts. */
class LeaveController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('manager.leavecontroller.index', $request); }
    public function approve(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('manager.leavecontroller.approve', $request); }
    public function reject(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('manager.leavecontroller.reject', $request); }
}
