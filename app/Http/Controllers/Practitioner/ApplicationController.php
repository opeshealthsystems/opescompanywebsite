<?php
namespace App\Http\Controllers\Practitioner;

/** Web presentation adapter; business queries and commands live in application contexts. */
class ApplicationController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('practitioner.applicationcontroller.index', $request); }
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('practitioner.applicationcontroller.show', $request); }
}
