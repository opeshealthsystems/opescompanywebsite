<?php
namespace App\Http\Controllers\Practitioner;

/** Web presentation adapter; business queries and commands live in application contexts. */
class SuggestionController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('practitioner.suggestioncontroller.index', $request); }
    public function store(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('practitioner.suggestioncontroller.store', $request); }
    public function create(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('practitioner.suggestioncontroller.create', $request); }
}
