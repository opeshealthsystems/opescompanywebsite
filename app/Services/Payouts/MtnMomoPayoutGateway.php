<?php

namespace App\Services\Payouts;

use App\Models\PractitionerApplication;
use Illuminate\Support\Facades\Http;

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

        // Use the stable reference already recorded by the API command.
        $reference = $options['reference'] ?? $application->payout_reference;
        if (! $reference) return PayoutResult::failed('A stable payout reference is required.');

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
            return PayoutResult::failed('MoMo transfer rejected ('.$response->status().').');
        }

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
