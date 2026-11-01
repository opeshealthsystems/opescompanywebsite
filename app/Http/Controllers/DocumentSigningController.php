<?php
namespace App\Http\Controllers;

/** Web presentation adapter; business queries and commands live in application contexts. */
class DocumentSigningController extends \App\Http\Controllers\Controller
{
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('documentsigningcontroller.show', $request); }
    public function sign(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('documentsigningcontroller.sign', $request); }
}
