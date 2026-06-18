<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->id === (int) $ticket->user_id
            || $user->hasAnyRole(['support', 'admin', 'super_admin']);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $this->view($user, $ticket);
    }

    public function reply(User $user, Ticket $ticket): bool
    {
        return $this->view($user, $ticket);
    }

    public function updateStatus(User $user, Ticket $ticket): bool
    {
        return $user->hasAnyRole(['support', 'admin', 'super_admin']);
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->hasAnyRole(['support', 'admin', 'super_admin']);
    }
}
