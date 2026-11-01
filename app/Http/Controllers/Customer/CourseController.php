<?php
namespace App\Http\Controllers\Customer;

/** Web presentation adapter; business queries and commands live in application contexts. */
class CourseController extends \App\Http\Controllers\Controller
{
    public function index(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('customer.coursecontroller.index', $request); }
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('customer.coursecontroller.show', $request); }
    public function enroll(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('customer.coursecontroller.enroll', $request); }
}
