<?php
namespace App\Http\Controllers;

/** Web presentation adapter; business queries and commands live in application contexts. */
class CertificatePdfController extends \App\Http\Controllers\Controller
{
    public function download(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('certificatepdfcontroller.download', $request); }
}
