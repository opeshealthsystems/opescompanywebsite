<?php

namespace App\Observers;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveApproved;
use App\Notifications\LeaveRejected;
use App\Notifications\LeaveRequestSubmitted;
use Illuminate\Support\Facades\Notification;

class LeaveRequestObserver
{
    public function created(LeaveRequest $leave): void
    {
        if ($leave->status !== 'pending') {
            return;
        }

        // whereHas (not the role() scope) so this never throws when the roles
        // are not registered — e.g. in model-only tests that skip the seeder.
        $reviewers = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['manager', 'hr']))->get();
        if ($reviewers->isNotEmpty()) {
            Notification::send($reviewers, new LeaveRequestSubmitted($leave));
        }
    }

    public function updated(LeaveRequest $leave): void
    {
        if (! $leave->wasChanged('status')) {
            return;
        }

        $notification = match ($leave->status) {
            'approved' => new LeaveApproved($leave),
            'rejected' => new LeaveRejected($leave),
            default    => null,
        };

        if ($notification) {
            $leave->employee?->notify($notification);
        }
    }
}
