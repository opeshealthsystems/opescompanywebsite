<?php

namespace App\Contexts\Programmes\Application\Commands;

use App\Mail\PayoutSettled;
use App\Models\PractitionerApplication;
use App\Notifications\FeedEntry;
use App\Services\Payouts\PayoutGatewayManager;
use App\Support\AdminNotifier;
use Illuminate\Support\Facades\Mail;

/** Scheduled application use case for reconciling provider payout states. */
final class ReconcilePendingPayouts
{
    public function __construct(
        private readonly PayoutGatewayManager $gateways,
        private readonly PayoutLifecycleCommands $lifecycle,
    ) {}

    public function execute(): array
    {
        $pending = PractitionerApplication::query()
            ->where('payout_status', 'pending')
            ->whereNotNull('payout_reference')
            ->whereIn('payout_provider', ['mtn', 'orange'])
            ->get();
        $result = ['checked' => 0, 'paid' => 0, 'failed' => 0, 'warnings' => []];

        foreach ($pending as $application) {
            $result['checked']++;
            try {
                $status = $this->gateways->driverFor($application->payout_provider)->status($application);
            } catch (\Throwable $error) {
                $result['warnings'][] = ['application_id' => $application->id, 'message' => $error->getMessage()];
                continue;
            }

            if ($status === 'paid') {
                if (!$this->lifecycle->markPaid($application->id)) continue;
                $result['paid']++;
                $application->refresh();
                if ($application->practitioner?->email) Mail::to($application->practitioner->email)->queue(new PayoutSettled($application));
                $application->practitioner?->notify(new FeedEntry(
                    'practitioner.payout_settled',
                    'Payout settled',
                    'Your payout of ' . $application->payout_amount . ' ' . $application->payout_currency . ' has been settled.',
                    'banknotes',
                    route('practitioner.dashboard', ['locale' => 'en']),
                ));
                AdminNotifier::notify(
                    'Payout settled',
                    $application->practitioner->name . ' was paid ' . $application->payout_amount . ' ' . $application->payout_currency,
                    null,
                );
            } elseif ($status === 'failed') {
                if (!$this->lifecycle->markFailed($application->id, $application->payout_failure_reason ?: 'Provider reported FAILED')) continue;
                $result['failed']++;
                AdminNotifier::notify(
                    'Payout failed',
                    'Payout for ' . $application->practitioner->name . ' failed.',
                    null,
                    ['super_admin', 'admin', 'support'],
                );
            }
        }

        return $result;
    }
}
