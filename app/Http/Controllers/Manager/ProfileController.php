<?php
namespace App\Http\Controllers\Manager;

/** Web presentation adapter; business queries and commands live in application contexts. */
class ProfileController extends \App\Http\Controllers\Controller
{
    public function show(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('manager.profilecontroller.show', $request); }
    public function update(\Illuminate\Http\Request $request) { return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('manager.profilecontroller.update', $request); }
}
