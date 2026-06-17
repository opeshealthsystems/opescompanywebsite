<?php

namespace App\Services\Payouts;

use App\Models\PractitionerApplication;
use RuntimeException;

/**
 * Orange Money payout driver — STUB.
 *
 * The other major Cameroon mobile-money rail. To activate:
 *   1. Set credentials in config/payouts.php (ORANGE_MONEY_* env vars), sandbox first.
 *   2. Implement disburse() against the Orange Money API:
 *        - obtain an OAuth access token (client_id + client_secret)
 *        - initiate a cash-transfer / web-payment to the practitioner's number
 *        - return PayoutResult::pending($reference) and reconcile via webhook/poll.
 *   3. Implement status() to query and map the transaction state to paid|pending.
 *
 * Left unimplemented on purpose (provider-specific, needs live credentials).
 */
class OrangeMoneyPayoutGateway implements PayoutGateway
{
    public function disburse(
        PractitionerApplication $application,
        float $amount,
        string $currency,
        array $options = []
    ): PayoutResult {
        throw new RuntimeException(
            'Orange Money payout driver is not yet implemented. Configure credentials in '
            . 'config/payouts.php and implement '.static::class.'::disburse(), or set '
            . "PAYOUT_DRIVER=manual to record payouts offline."
        );
    }

    public function status(PractitionerApplication $application): string
    {
        return $application->payout_status;
    }
}
