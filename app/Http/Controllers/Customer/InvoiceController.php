<?php
namespace App\Http\Controllers\Customer;

/** Web presentation adapter; business queries and commands live in application contexts. */
class InvoiceController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('customer.invoicecontroller.index', $request); }
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('customer.invoicecontroller.show', $request); }
}
