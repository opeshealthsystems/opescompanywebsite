<?php

namespace App\Services\Payouts;

/**
 * Outcome of a payout disbursement attempt. Provider-agnostic so the calling
 * code (admin "Record Payout" action) never depends on a specific rail.
 */
class PayoutResult
{
    public function __construct(
        public bool $success,
        public string $status,            // matches PractitionerApplication payout_status: pending|paid
        public ?string $reference = null, // provider/transaction reference
        public ?string $message = null,
    ) {
    }

    public static function settled(?string $reference = null): self
    {
        return new self(true, 'paid', $reference, 'Payout recorded.');
    }

    public static function pending(?string $reference = null, ?string $message = null): self
    {
        return new self(true, 'pending', $reference, $message ?? 'Payout initiated; awaiting confirmation.');
    }

    public static function failed(string $message): self
    {
        return new self(false, 'pending', null, $message);
    }
}
