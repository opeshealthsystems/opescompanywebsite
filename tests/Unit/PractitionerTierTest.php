<?php

namespace Tests\Unit;

use App\Enums\PractitionerTier;
use PHPUnit\Framework\TestCase;

class PractitionerTierTest extends TestCase
{
    public function test_unverified_is_always_associate_regardless_of_findings(): void
    {
        $this->assertSame(PractitionerTier::Associate, PractitionerTier::forProfile(false, 0));
        $this->assertSame(PractitionerTier::Associate, PractitionerTier::forProfile(false, 10));
    }

    public function test_verified_findings_thresholds(): void
    {
        $this->assertSame(PractitionerTier::Verified, PractitionerTier::forProfile(true, 0));
        $this->assertSame(PractitionerTier::Verified, PractitionerTier::forProfile(true, 2));
        $this->assertSame(PractitionerTier::Distinguished, PractitionerTier::forProfile(true, 3));
        $this->assertSame(PractitionerTier::Distinguished, PractitionerTier::forProfile(true, 7));
        $this->assertSame(PractitionerTier::Fellow, PractitionerTier::forProfile(true, 8));
        $this->assertSame(PractitionerTier::Fellow, PractitionerTier::forProfile(true, 50));
    }

    public function test_only_verified_and_above_can_apply_to_paid(): void
    {
        $this->assertFalse(PractitionerTier::Associate->canApplyToPaid());
        $this->assertTrue(PractitionerTier::Verified->canApplyToPaid());
        $this->assertTrue(PractitionerTier::Distinguished->canApplyToPaid());
        $this->assertTrue(PractitionerTier::Fellow->canApplyToPaid());
    }

    public function test_levels_are_strictly_increasing(): void
    {
        $this->assertSame(0, PractitionerTier::Associate->level());
        $this->assertSame(1, PractitionerTier::Verified->level());
        $this->assertSame(2, PractitionerTier::Distinguished->level());
        $this->assertSame(3, PractitionerTier::Fellow->level());
    }

    public function test_next_returns_following_tier_or_null_at_top(): void
    {
        $this->assertSame(PractitionerTier::Verified, PractitionerTier::Associate->next());
        $this->assertSame(PractitionerTier::Fellow, PractitionerTier::Distinguished->next());
        $this->assertNull(PractitionerTier::Fellow->next());
    }
}
