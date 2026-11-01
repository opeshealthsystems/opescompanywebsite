<?php
namespace App\Http\Controllers\Practitioner\Validation;

/** Web presentation adapter; business queries and commands live in application contexts. */
class RetestController extends \App\Http\Controllers\Controller
{
    public function store(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('practitioner.validation.retestcontroller.store', $request); }
}
