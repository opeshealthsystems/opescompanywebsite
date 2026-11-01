<?php
namespace App\Http\Controllers\Support;

/** Web presentation adapter; business queries and commands live in application contexts. */
class TicketController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('support.ticketcontroller.index', $request); }
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('support.ticketcontroller.show', $request); }
    public function assignToMe(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('support.ticketcontroller.assignToMe', $request); }
    public function reply(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('support.ticketcontroller.reply', $request); }
    public function updateStatus(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('support.ticketcontroller.updateStatus', $request); }
}
