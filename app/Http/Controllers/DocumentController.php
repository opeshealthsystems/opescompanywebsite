<?php
namespace App\Http\Controllers;

/** Web presentation adapter; business queries and commands live in application contexts. */
class DocumentController extends \App\Http\Controllers\Controller
{
    public function pdf(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('documentcontroller.pdf', $request); }
    public function preview(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('documentcontroller.preview', $request); }
}
