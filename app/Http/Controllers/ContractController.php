<?php
namespace App\Http\Controllers;

/** Web presentation adapter; business queries and commands live in application contexts. */
class ContractController extends \App\Http\Controllers\Controller
{
    public function pdf(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('contractcontroller.pdf', $request); }
}
