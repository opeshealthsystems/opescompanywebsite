<?php

namespace App\Services\Payouts;

use App\Models\PractitionerApplication;

/**
 * A payout rail for paying practitioners who participated in a paid testing
 * programme. The manual driver settles offline; provider drivers (MTN MoMo,
 * Orange Money) disburse to a mobile-money account. The interface is kept
 * deliberately provider-agnostic so the admin flow never changes when a real
 * rail is plugged in.
 */
interface PayoutGateway
{
    /**
     * Disburse a payout for the given approved application and persist the
     * outcome onto it (payout_status, amount, currency, reference, paid_at).
     *
     * @param  array{reference?: string|null}  $options
     */
    public function disburse(
        PractitionerApplication $application,
        float $amount,
        string $currency,
        array $options = []
    ): PayoutResult;

    /**
     * Current payout status for the application (e.g. after polling a provider).
     */
    public function status(PractitionerApplication $application): string;
}
