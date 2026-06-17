<?php

namespace App\Enums;

enum PractitionerTier: string
{
    case Associate = 'associate';
    case Verified = 'verified';
    case Distinguished = 'distinguished';
    case Fellow = 'fellow';

    /** Published-findings thresholds (only apply once verified). */
    public const DISTINGUISHED_FINDINGS = 3;
    public const FELLOW_FINDINGS = 8;

    /** Derive a tier from verification status + published-findings count. */
    public static function forProfile(bool $isVerified, int $publishedFindings): self
    {
        if (! $isVerified) {
            return self::Associate;
        }

        if ($publishedFindings >= self::FELLOW_FINDINGS) {
            return self::Fellow;
        }

        if ($publishedFindings >= self::DISTINGUISHED_FINDINGS) {
            return self::Distinguished;
        }

        return self::Verified;
    }

    public function label(): string
    {
        return match ($this) {
            self::Associate => 'Associate',
            self::Verified => 'Verified',
            self::Distinguished => 'Distinguished',
            self::Fellow => 'Fellow',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Associate => 'Open to volunteer programmes. Get verified to unlock paid programmes.',
            self::Verified => 'Verified practitioner — eligible for paid programmes.',
            self::Distinguished => 'Distinguished — recognised for sustained, published contributions.',
            self::Fellow => 'Fellow — the highest standing, with top priority on paid programmes.',
        };
    }

    public function level(): int
    {
        return match ($this) {
            self::Associate => 0,
            self::Verified => 1,
            self::Distinguished => 2,
            self::Fellow => 3,
        };
    }

    public function canApplyToPaid(): bool
    {
        return $this->level() >= self::Verified->level();
    }

    /** Tailwind chip classes for blade views. */
    public function tailwindBadge(): string
    {
        return match ($this) {
            self::Associate => 'bg-slate-700 text-slate-300',
            self::Verified => 'bg-emerald-900 text-emerald-300',
            self::Distinguished => 'bg-sky-900 text-sky-300',
            self::Fellow => 'bg-amber-900 text-amber-300',
        };
    }

    /** Filament badge color name. */
    public function filamentColor(): string
    {
        return match ($this) {
            self::Associate => 'gray',
            self::Verified => 'success',
            self::Distinguished => 'info',
            self::Fellow => 'warning',
        };
    }

    /** The next rung up, or null at the top. */
    public function next(): ?self
    {
        return match ($this) {
            self::Associate => self::Verified,
            self::Verified => self::Distinguished,
            self::Distinguished => self::Fellow,
            self::Fellow => null,
        };
    }
}
