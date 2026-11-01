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
     * Submit a payout request to the provider and return its outcome. Application
     * state changes only through the shared Programme lifecycle commands.
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
     * Current payout status reported by the provider (e.g. during reconciliation).
     */
    public function status(PractitionerApplication $application): string;
}
