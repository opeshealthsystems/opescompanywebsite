<?php

namespace App\Services\Payouts;

use App\Models\PractitionerApplication;
use RuntimeException;

/**
 * MTN Mobile Money (MoMo) Disbursements driver — STUB.
 *
 * The dominant mobile-money payout rail in Cameroon. To activate:
 *   1. Set credentials in config/payouts.php (MTN_MOMO_* env vars), sandbox first.
 *   2. Implement disburse() against the MoMo Disbursements API:
 *        - obtain an access token (api_user + api_key + subscription_key)
 *        - POST /disbursement/v1_0/transfer with the practitioner's MoMo number,
 *          amount, currency, and an idempotent referenceId
 *        - return PayoutResult::pending($referenceId) and reconcile via webhook/poll.
 *   3. Implement status() to GET the transfer status and map to paid|pending.
 *
 * Left unimplemented on purpose: building it requires live MoMo credentials and
 * is provider-specific. The seam (interface + config + container binding) is
 * ready so this becomes a drop-in.
 */
class MtnMomoPayoutGateway implements PayoutGateway
{
    public function disburse(
        PractitionerApplication $application,
        float $amount,
        string $currency,
        array $options = []
    ): PayoutResult {
        throw new RuntimeException(
            'MTN MoMo payout driver is not yet implemented. Configure credentials in '
            . 'config/payouts.php and implement '.static::class.'::disburse(), or set '
            . "PAYOUT_DRIVER=manual to record payouts offline."
        );
    }

    public function status(PractitionerApplication $application): string
    {
        return $application->payout_status;
    }
}
