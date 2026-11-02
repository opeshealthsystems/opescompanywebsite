<?php

namespace App\Contexts\Publishing\Application\Commands;

use App\Mail\ContractExpiryWarning;
use App\Models\Contract;
use Illuminate\Support\Facades\Mail;

/** Scheduled application use case for contract expiry reminders. */
final class SendContractExpiryWarnings
{
    public function execute(): int
    {
        $sent = 0;
        foreach ([30, 7] as $days) {
            $target = now()->addDays($days)->toDateString();
            $contracts = Contract::with('lead')->where('status', 'active')->where('auto_renew', false)
                ->whereDate('end_date', $target)->whereHas('lead')->get();

            foreach ($contracts as $contract) {
                $email = $contract->lead->email ?? null;
                if (!$email) continue;

                Mail::to($email)->queue(new ContractExpiryWarning($contract, $days));
                $sent++;
            }
        }
        return $sent;
    }
}
