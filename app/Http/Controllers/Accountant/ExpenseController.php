<?php
namespace App\Http\Controllers\Accountant;

/** Web presentation adapter; business queries and commands live in application contexts. */
class ExpenseController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('accountant.expensecontroller.index', $request); }
    public function approve(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('accountant.expensecontroller.approve', $request); }
    public function reject(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('accountant.expensecontroller.reject', $request); }
}
