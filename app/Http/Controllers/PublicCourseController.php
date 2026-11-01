<?php
namespace App\Http\Controllers;

/** Web presentation adapter; business queries and commands live in application contexts. */
class PublicCourseController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('publiccoursecontroller.index', $request); }
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('publiccoursecontroller.show', $request); }
}
