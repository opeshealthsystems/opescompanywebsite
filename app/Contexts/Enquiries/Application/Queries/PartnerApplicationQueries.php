<?php

namespace App\Contexts\Enquiries\Application\Queries;

use App\Models\PartnerApplication;
use App\Models\User;
use Filament\Notifications\Notification;
class PartnerApplicationQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        return \App\Contexts\Shared\Application\WorkflowResult::read('partnerapplicationcontroller_show_1', []);
    }
}
