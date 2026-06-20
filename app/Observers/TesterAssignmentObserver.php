<?php

namespace App\Observers;

use App\Models\TesterAssignment;
use App\Notifications\NewTesterAssignment;

class TesterAssignmentObserver
{
    public function created(TesterAssignment $assignment): void
    {
        $assignment->tester?->notify(new NewTesterAssignment($assignment));
    }
}
