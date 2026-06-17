<?php

namespace App\Console\Commands;

use App\Mail\PayoutSettled;
use App\Models\PractitionerApplication;
use App\Services\Payouts\PayoutGatewayManager;
use App\Support\AdminNotifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class PollPayouts extends Command
{
    protected $signature = 'payouts:poll';

    protected $description = 'Poll pending mobile-money payouts and update their status.';

    public function handle(PayoutGatewayManager $manager): int
    {
        $pending = PractitionerApplication::query()
            ->where('payout_status', 'pending')
            ->whereNotNull('payout_reference')
            ->whereIn('payout_provider', ['mtn', 'orange'])
            ->get();

        foreach ($pending as $application) {
            try {
                $status = $manager->driverFor($application->payout_provider)->status($application);
            } catch (\Throwable $e) {
                $this->warn("Payout {$application->id}: {$e->getMessage()}");
                continue;
            }

            if ($status === 'paid') {
                $application->update(['payout_status' => 'paid', 'paid_at' => now()]);
                Mail::to($application->practitioner->email)->queue(new PayoutSettled($application));
                AdminNotifier::notify(
                    'Payout settled',
                    $application->practitioner->name.' was paid '.$application->payout_amount.' '.$application->payout_currency,
                    null,
                );
            } elseif ($status === 'failed') {
                $application->update([
                    'payout_status'         => 'failed',
                    'payout_failure_reason' => $application->payout_failure_reason ?: 'Provider reported FAILED',
                ]);
                AdminNotifier::notify(
                    'Payout failed',
                    'Payout for '.$application->practitioner->name.' failed.',
                    null,
                    ['super_admin', 'admin', 'support'],
                );
            }
        }

        return self::SUCCESS;
    }
}
