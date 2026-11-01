<?php
namespace App\Http\Controllers\Public;

/** Web presentation adapter; business queries and commands live in application contexts. */
class PractitionerDirectoryController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('public.practitionerdirectorycontroller.index', $request); }
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('public.practitionerdirectorycontroller.show', $request); }
}
