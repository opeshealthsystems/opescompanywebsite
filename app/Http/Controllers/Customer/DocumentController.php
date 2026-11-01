<?php
namespace App\Http\Controllers\Customer;

/** Web presentation adapter; business queries and commands live in application contexts. */
class DocumentController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('customer.documentcontroller.index', $request); }
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('customer.documentcontroller.show', $request); }
}
