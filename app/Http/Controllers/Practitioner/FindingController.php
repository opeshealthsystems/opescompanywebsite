<?php
namespace App\Http\Controllers\Practitioner;

/** Web presentation adapter; business queries and commands live in application contexts. */
class FindingController extends \App\Http\Controllers\Controller
{
    public function store(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('practitioner.findingcontroller.store', $request); }
    public function create(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('practitioner.findingcontroller.create', $request); }
}
