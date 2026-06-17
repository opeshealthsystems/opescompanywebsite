<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Provisions an MTN MoMo *sandbox* API User + API Key from the subscription key,
 * so the operator only needs the subscription key to get going. Prints the
 * values to paste into .env — it does NOT write .env itself.
 *
 *   php artisan momo:provision
 */
class ProvisionMomoApiUser extends Command
{
    protected $signature = 'momo:provision {--callback=https://opeshealthsystems.com : providerCallbackHost}';

    protected $description = 'Provision an MTN MoMo sandbox API user + API key from the subscription key.';

    public function handle(): int
    {
        $cfg = config('payouts.mtn_momo');

        if (empty($cfg['subscription_key'])) {
            $this->error('MTN_MOMO_SUBSCRIPTION_KEY is not set. Add it to .env first.');

            return self::FAILURE;
        }

        $base = rtrim($cfg['base_url'] ?: 'https://sandbox.momodeveloper.mtn.com', '/');
        $apiUser = (string) Str::uuid();

        $create = Http::withHeaders([
            'X-Reference-Id'            => $apiUser,
            'Ocp-Apim-Subscription-Key' => $cfg['subscription_key'],
            'Content-Type'              => 'application/json',
        ])->post($base.'/v1_0/apiuser', [
            'providerCallbackHost' => $this->option('callback'),
        ]);

        if ($create->status() !== 201) {
            $this->error('Failed to create API user ('.$create->status().'): '.$create->body());

            return self::FAILURE;
        }

        $keyResponse = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $cfg['subscription_key'],
        ])->post($base.'/v1_0/apiuser/'.$apiUser.'/apikey');

        if ($keyResponse->status() !== 201) {
            $this->error('Failed to create API key ('.$keyResponse->status().'): '.$keyResponse->body());

            return self::FAILURE;
        }

        $apiKey = (string) $keyResponse->json('apiKey');

        $this->info('MoMo API user provisioned. Add these to your .env (then run config:clear):');
        $this->line('MTN_MOMO_API_USER='.$apiUser);
        $this->line('MTN_MOMO_API_KEY='.$apiKey);

        return self::SUCCESS;
    }
}
