<?php
namespace App\Http\Controllers\Manager;

/** Web presentation adapter; business queries and commands live in application contexts. */
class ReportController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('manager.reportcontroller.index', $request); }
}
