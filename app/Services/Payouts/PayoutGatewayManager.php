<?php

namespace App\Services\Payouts;

use App\Models\PractitionerApplication;
use Illuminate\Contracts\Container\Container;

/**
 * Selects the payout driver for a given payout based on the practitioner's
 * resolved mobile-money network. Falls back to the configured default
 * (manual) driver when no network can be determined.
 */
class PayoutGatewayManager
{
    public function __construct(private Container $app)
    {
    }

    public function driverFor(?string $network): PayoutGateway
    {
        return match ($network) {
            'mtn'    => $this->app->make(MtnMomoPayoutGateway::class),
            'orange' => $this->app->make(OrangeMoneyPayoutGateway::class),
            default  => $this->app->make(PayoutGateway::class), // manual / configured default
        };
    }

    /**
     * Network for this application: explicit override, else detected from the
     * practitioner's payout number, else 'manual'.
     */
    public function resolveNetwork(PractitionerApplication $application, ?string $override = null): string
    {
        if ($override) {
            return $override;
        }

        $number = $application->practitioner?->practitionerProfile?->payout_number;
        if (! $number) {
            return 'manual';
        }

        return MobileMoneyNetwork::detect($number) ?? 'manual';
    }
}
