<?php

namespace App\Policies;

use App\Models\TesterAssignment;
use App\Models\User;

class TesterAssignmentPolicy
{
    public function view(User $user, TesterAssignment $assignment): bool
    {
        return $user->id === $assignment->assigned_to
            || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function update(User $user, TesterAssignment $assignment): bool
    {
        return $this->view($user, $assignment);
    }
}
