<?php
namespace App\Http\Controllers\Practitioner\Validation;

/** Web presentation adapter; business queries and commands live in application contexts. */
class DashboardController extends \App\Http\Controllers\Controller
{
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('practitioner.validation.dashboardcontroller.show', $request); }
}
