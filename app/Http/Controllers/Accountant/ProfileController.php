<?php
namespace App\Http\Controllers\Accountant;

/** Web presentation adapter; business queries and commands live in application contexts. */
class ProfileController extends \App\Http\Controllers\Controller
{
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('accountant.profilecontroller.show', $request); }
    public function update(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('accountant.profilecontroller.update', $request); }
}
