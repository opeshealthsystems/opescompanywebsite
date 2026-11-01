<?php
namespace App\Http\Controllers\HR;

/** Web presentation adapter; business queries and commands live in application contexts. */
class PerformanceController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('hr.performancecontroller.index', $request); }
    public function store(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('hr.performancecontroller.store', $request); }
}
