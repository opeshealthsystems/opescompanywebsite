<?php

namespace App\Policies;

use App\Models\PractitionerFinding;
use App\Models\User;

class PractitionerFindingPolicy
{
    public function view(User $user, PractitionerFinding $finding): bool
    {
        return $user->id === $finding->practitioner_id
            || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('practitioner');
    }

    public function update(User $user, PractitionerFinding $finding): bool
    {
        return $user->id === $finding->practitioner_id
            && ! $finding->is_published;
    }

    public function delete(User $user, PractitionerFinding $finding): bool
    {
        return ($user->id === $finding->practitioner_id && ! $finding->is_published)
            || $user->hasAnyRole(['admin', 'super_admin']);
    }
}
