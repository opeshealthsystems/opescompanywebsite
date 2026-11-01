<?php

namespace App\Contexts\Identity\Application\Queries;

class NotificationQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $notifications = $request->paginate($request->user()->notifications(), 20);
        return \App\Contexts\Shared\Application\WorkflowResult::read('notificationcontroller_index_1', compact('notifications'));
    }
}
