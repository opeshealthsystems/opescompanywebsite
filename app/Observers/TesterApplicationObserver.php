<?php

namespace App\Observers;

use App\Models\TesterApplication;
use App\Models\User;
use App\Notifications\TesterApplicationApproved;
use App\Notifications\TesterApplicationRejected;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Notification;

class TesterApplicationObserver
{
    public function updated(TesterApplication $app): void
    {
        if (! $app->wasChanged('status')) {
            return;
        }

        match ($app->status) {
            'accepted' => $this->notifyApplicant($app, new TesterApplicationApproved($app->name)),
            'rejected' => $this->notifyApplicant($app, new TesterApplicationRejected($app->name, $app->admin_notes)),
            default    => null,
        };
    }

    private function notifyApplicant(TesterApplication $app, BaseNotification $notification): void
    {
        $user = User::where('email', $app->email)->first();

        $user
            ? $user->notify($notification)
            : Notification::route('mail', $app->email)->notify($notification);
    }
}
