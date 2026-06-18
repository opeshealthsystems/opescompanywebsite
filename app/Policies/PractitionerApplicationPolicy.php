<?php

namespace App\Policies;

use App\Models\PractitionerApplication;
use App\Models\User;

class PractitionerApplicationPolicy
{
    public function view(User $user, PractitionerApplication $application): bool
    {
        return $user->id === $application->practitioner_id
            || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function update(User $user, PractitionerApplication $application): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }
}
