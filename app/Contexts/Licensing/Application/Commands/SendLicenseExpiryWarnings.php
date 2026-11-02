<?php

namespace App\Contexts\Licensing\Application\Commands;

use App\Mail\LicenseExpiryWarning;
use App\Models\License;
use App\Notifications\FeedEntry;
use Illuminate\Support\Facades\Mail;

/** Scheduled application use case for license expiry reminders. */
final class SendLicenseExpiryWarnings
{
    public function execute(): int
    {
        $sent = 0;
        foreach ([30, 7] as $days) {
            $target = now()->addDays($days)->toDateString();
            $licenses = License::with('customer')->where('status', 'active')->whereDate('end_date', $target)->whereHas('customer')->get();

            foreach ($licenses as $license) {
                $email = $license->customer->email ?? null;
                if (!$email) continue;

                Mail::to($email)->queue(new LicenseExpiryWarning($license, $days));
                $license->customer?->notify(new FeedEntry(
                    'licensing.expiry', 'License expiring soon', $license->product_name . ' expires in ' . $days . ' days.',
                    'key', route('customer.licenses.show', ['locale' => 'en', 'id' => $license->id]),
                ));
                $sent++;
            }
        }
        return $sent;
    }
}
