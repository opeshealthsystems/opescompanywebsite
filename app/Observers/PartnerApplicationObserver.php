<?php

namespace App\Observers;

use App\Models\PartnerApplication;
use App\Notifications\PartnerApplicationApproved;
use App\Notifications\PartnerApplicationRejected;
use Illuminate\Support\Facades\Notification;

class PartnerApplicationObserver
{
    public function updated(PartnerApplication $app): void
    {
        if (! $app->wasChanged('status')) {
            return;
        }

        $notification = match ($app->status) {
            'approved' => new PartnerApplicationApproved($app->contact_name, $app->organization_name),
            'rejected' => new PartnerApplicationRejected($app->contact_name, $app->organization_name, $app->admin_notes),
            default    => null,
        };

        if ($notification) {
            Notification::route('mail', $app->email)->notify($notification);
        }
    }
}
