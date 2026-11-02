<?php

namespace App\Console\Commands;

use App\Contexts\Programmes\Application\Commands\ReconcilePendingPayouts;
use Illuminate\Console\Command;

class PollPayouts extends Command
{
    protected $signature = 'payouts:poll';
    protected $description = 'Poll pending mobile-money payouts and update their status.';

    public function handle(ReconcilePendingPayouts $reconciliation): int
    {
        $result = $reconciliation->execute();
        foreach ($result['warnings'] as $warning) {
            $this->warn('Payout ' . $warning['application_id'] . ': ' . $warning['message']);
        }

        return self::SUCCESS;
    }
}
