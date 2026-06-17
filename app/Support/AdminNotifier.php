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
        // Best-effort: a missing role / notification failure must never break the
        // caller's core flow (e.g. a payout settlement).
        try {
            $recipients = User::role($roles)->get();
        } catch (\Throwable $e) {
            return;
        }

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
