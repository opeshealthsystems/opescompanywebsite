<?php
namespace App\Http\Controllers\Tester;

/** Web presentation adapter; business queries and commands live in application contexts. */
class DashboardController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('tester.dashboardcontroller.index', $request); }
}
