<?php

namespace App\Services\Payouts;

/**
 * Resolves a Cameroon mobile-money number to its network (MTN or Orange) by
 * prefix. Unknown prefixes return null so the admin must pick the network
 * explicitly at payout time.
 */
class MobileMoneyNetwork
{
    /** Strip spaces/+, drop the 237 country code, return the local 9-digit MSISDN. */
    public static function normalise(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number) ?? '';

        if (str_starts_with($digits, '237') && strlen($digits) > 9) {
            $digits = substr($digits, 3);
        }

        return $digits;
    }

    /**
     * @return 'mtn'|'orange'|null
     */
    public static function detect(string $number): ?string
    {
        $n = self::normalise($number);

        if (strlen($n) < 3) {
            return null;
        }

        $p2 = substr($n, 0, 2);
        $p3 = (int) substr($n, 0, 3);

        if ($p2 === '67' || ($p3 >= 650 && $p3 <= 654) || ($p3 >= 680 && $p3 <= 689)) {
            return 'mtn';
        }

        if ($p2 === '69' || ($p3 >= 655 && $p3 <= 659) || ($p3 >= 640 && $p3 <= 649)) {
            return 'orange';
        }

        return null;
    }
}
