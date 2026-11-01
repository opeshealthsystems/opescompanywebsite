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

        return PayoutResult::settled($reference);
    }

    public function status(PractitionerApplication $application): string
    {
        return $application->payout_status;
    }
}
