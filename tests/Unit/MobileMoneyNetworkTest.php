<?php

namespace Tests\Unit;

use App\Services\Payouts\MobileMoneyNetwork;
use PHPUnit\Framework\TestCase;

class MobileMoneyNetworkTest extends TestCase
{
    public function test_normalise_strips_spaces_plus_and_country_code(): void
    {
        $this->assertSame('677123456', MobileMoneyNetwork::normalise('+237 677 123 456'));
        $this->assertSame('699000111', MobileMoneyNetwork::normalise('237699000111'));
        $this->assertSame('680111222', MobileMoneyNetwork::normalise('680 111 222'));
    }

    public function test_detect_mtn_numbers(): void
    {
        $this->assertSame('mtn', MobileMoneyNetwork::detect('+237 677 12 34 56')); // 67x
        $this->assertSame('mtn', MobileMoneyNetwork::detect('650111222'));          // 650-654
        $this->assertSame('mtn', MobileMoneyNetwork::detect('680111222'));          // 680-689
    }

    public function test_detect_orange_numbers(): void
    {
        $this->assertSame('orange', MobileMoneyNetwork::detect('+237 699 12 34 56')); // 69x
        $this->assertSame('orange', MobileMoneyNetwork::detect('655111222'));          // 655-659
        $this->assertSame('orange', MobileMoneyNetwork::detect('640111222'));          // 640-649
    }

    public function test_detect_unknown_returns_null(): void
    {
        $this->assertNull(MobileMoneyNetwork::detect('620111222')); // other operator
        $this->assertNull(MobileMoneyNetwork::detect(''));
    }
}
