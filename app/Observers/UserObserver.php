<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\AccountDeactivated;

class UserObserver
{
    public function updated(User $user): void
    {
        // Fire only on a true→false transition of is_active (deactivation),
        // not on creation or unrelated updates.
        if ($user->wasChanged('is_active') && ! $user->is_active) {
            $user->notify(new AccountDeactivated());
        }
    }
}
