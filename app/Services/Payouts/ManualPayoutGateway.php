<?php

namespace App\Services\Payouts;

use App\Models\PractitionerApplication;

/**
 * Default driver: an admin settles the payout offline (cash, bank transfer,
 * mobile money done by hand) and records it here. No external API call.
 */
class ManualPayoutGateway implements PayoutGateway
{
    public function disburse(
        PractitionerApplication $application,
        float $amount,
        string $currency,
        array $options = []
    ): PayoutResult {
        $reference = $options['reference'] ?? null;

        $application->update([
            'payout_status'    => 'paid',
            'payout_amount'    => $amount,
            'payout_currency'  => $currency,
            'payout_reference' => $reference,
            'paid_at'          => now(),
        ]);

        return PayoutResult::settled($reference);
    }

    public function status(PractitionerApplication $application): string
    {
        return $application->payout_status;
    }
}
