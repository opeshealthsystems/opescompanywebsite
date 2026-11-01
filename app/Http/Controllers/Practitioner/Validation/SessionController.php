<?php
namespace App\Http\Controllers\Practitioner\Validation;

/** Web presentation adapter; business queries and commands live in application contexts. */
class SessionController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('practitioner.validation.sessioncontroller.index', $request); }
    public function store(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('practitioner.validation.sessioncontroller.store', $request); }
    public function create(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('practitioner.validation.sessioncontroller.create', $request); }
}
