<?php

namespace App\Contexts\CustomerServices\Application\Queries;

use App\Models\License;
use Illuminate\Support\Facades\Auth;
class CustomerLicenseQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $licenses = $workflowInput->paginate(\App\Models\License::where('user_id', $user->id)->orderByDesc('created_at'), 15);
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_licensecontroller_index_1', compact('licenses'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $id = (int) $request->route('id');
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $license = \App\Models\License::findOrFail($id);
        abort_if((int) $license->user_id !== $user->id, 403);
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_licensecontroller_show_1', compact('license'));
    }
}
