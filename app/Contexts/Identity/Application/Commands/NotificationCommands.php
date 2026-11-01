<?php

namespace App\Contexts\Identity\Application\Commands;

class NotificationCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function markAllRead(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $request->user()->unreadNotifications->markAsRead();
        return \App\Contexts\Shared\Application\WorkflowResult::next('notificationcontroller_markAllRead_1')->with('success', 'All notifications marked as read.');
    }
    public function markRead($locale, string $id, \App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        $url = $notification->data['url'] ?? null;
        return $url ? \App\Contexts\Shared\Application\WorkflowResult::next('notificationcontroller_markRead_1', ['url' => $url]) : \App\Contexts\Shared\Application\WorkflowResult::next('notificationcontroller_markRead_2');
    }
}
