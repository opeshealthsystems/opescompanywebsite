<?php

namespace App\Services\Payouts;

use App\Models\PractitionerApplication;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * MTN Mobile Money (MoMo) Disbursements driver.
 *
 * Initiates a transfer to the practitioner's MSISDN and reports status. A 202
 * from /transfer means the request is *queued*, not settled — final state is
 * confirmed by polling status() (see the payouts:poll command).
 *
 * Tested against mocked HTTP. Live verification requires real sandbox
 * credentials in config/payouts.php (MTN_MOMO_* env vars).
 */
class MtnMomoPayoutGateway implements PayoutGateway
{
    private function config(): array
    {
        return config('payouts.mtn_momo');
    }

    private function token(): string
    {
        $cfg = $this->config();

        $response = Http::withBasicAuth($cfg['api_user'], $cfg['api_key'])
            ->withHeaders(['Ocp-Apim-Subscription-Key' => $cfg['subscription_key']])
            ->post(rtrim($cfg['base_url'], '/').'/disbursement/token/');

        $response->throw();

        return (string) $response->json('access_token');
    }

    public function disburse(PractitionerApplication $application, float $amount, string $currency, array $options = []): PayoutResult
    {
        $cfg = $this->config();

        $number = $application->practitioner?->practitionerProfile?->payout_number;
        if (! $number) {
            return PayoutResult::failed('Practitioner has no mobile-money number on file.');
        }

        // Reuse an existing reference so retries are idempotent and never double-pay.
        $reference = $application->payout_reference ?: (string) Str::uuid();

        $application->update([
            'payout_reference'    => $reference,
            'payout_provider'     => 'mtn',
            'payout_amount'       => $amount,
            'payout_currency'     => $currency,
            'payout_initiated_at' => now(),
        ]);

        // MoMo Cameroon expects the full international MSISDN (country code, no +).
        $partyId = '237'.MobileMoneyNetwork::normalise($number);

        $response = Http::withToken($this->token())
            ->withHeaders([
                'X-Reference-Id'            => $reference,
                'X-Target-Environment'      => $cfg['environment'],
                'Ocp-Apim-Subscription-Key' => $cfg['subscription_key'],
            ])
            ->post(rtrim($cfg['base_url'], '/').'/disbursement/v1_0/transfer', [
                'amount'       => (string) $amount,
                'currency'     => $currency,
                'externalId'   => (string) $application->id,
                'payee'        => ['partyIdType' => 'MSISDN', 'partyId' => $partyId],
                'payerMessage' => 'OPES practitioner payout',
                'payeeNote'    => 'OPES payout '.$reference,
            ]);

        if ($response->status() !== 202) {
            $application->update([
                'payout_status'         => 'failed',
                'payout_failure_reason' => 'MoMo transfer rejected ('.$response->status().')',
            ]);

            return PayoutResult::failed('MoMo transfer rejected ('.$response->status().').');
        }

        $application->update(['payout_status' => 'pending']);

        return PayoutResult::pending($reference, 'MoMo transfer queued.');
    }

    public function status(PractitionerApplication $application): string
    {
        $cfg = $this->config();

        $response = Http::withToken($this->token())
            ->withHeaders([
                'X-Target-Environment'      => $cfg['environment'],
                'Ocp-Apim-Subscription-Key' => $cfg['subscription_key'],
            ])
            ->get(rtrim($cfg['base_url'], '/').'/disbursement/v1_0/transfer/'.$application->payout_reference);

        return match (strtoupper((string) $response->json('status'))) {
            'SUCCESSFUL' => 'paid',
            'FAILED'     => 'failed',
            default      => 'pending',
        };
    }
}
