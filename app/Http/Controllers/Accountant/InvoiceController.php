<?php
namespace App\Http\Controllers\Accountant;

/** Web presentation adapter; business queries and commands live in application contexts. */
class InvoiceController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('accountant.invoicecontroller.index', $request); }
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('accountant.invoicecontroller.show', $request); }
    public function markPaid(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('accountant.invoicecontroller.markPaid', $request); }
}
