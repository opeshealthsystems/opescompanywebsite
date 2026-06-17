<?php

namespace App\Support;

use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class AdminNotifier
{
    /**
     * Send a Filament database notification to all staff with the given roles.
     *
     * @param array<int,string> $roles
     */
    public static function notify(string $title, string $body, ?string $url = null, array $roles = ['super_admin', 'admin']): void
    {
        $recipients = User::role($roles)->get();
        if ($recipients->isEmpty()) {
            return;
        }

        $notification = Notification::make()
            ->title($title)
            ->body($body)
            ->icon('heroicon-o-bell');

        if ($url) {
            $notification->actions([
                Action::make('view')->label('View')->url($url)->markAsRead(),
            ]);
        }

        $notification->sendToDatabase($recipients);
    }
}
