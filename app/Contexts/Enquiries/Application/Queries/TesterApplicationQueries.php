<?php

namespace App\Contexts\Enquiries\Application\Queries;

use App\Models\TesterApplication;
use App\Models\User;
use Filament\Notifications\Notification;
class TesterApplicationQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        return \App\Contexts\Shared\Application\WorkflowResult::read('testerapplicationcontroller_show_1', []);
    }
}
