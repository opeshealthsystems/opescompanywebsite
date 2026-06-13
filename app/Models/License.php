<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class License extends Model
{
    protected $fillable = [
        'user_id', 'issued_by', 'product_slug', 'product_name',
        'license_key', 'plan', 'seats', 'status',
        'start_date', 'end_date', 'price', 'currency', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'seats'      => 'integer',
        'price'      => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public static function generateKey(): string
    {
        return 'OPES-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->status === 'active'
            && $this->end_date !== null
            && $this->end_date->isFuture()
            && $this->end_date->lte(now()->addDays($days));
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->end_date !== null && $this->end_date->isPast());
    }

    public static function planLabel(string $plan): string
    {
        return match ($plan) {
            'starter'      => 'Starter',
            'standard'     => 'Standard',
            'professional' => 'Professional',
            'enterprise'   => 'Enterprise',
            default        => ucfirst($plan),
        };
    }

    public static function planOptions(): array
    {
        return [
            'starter'      => 'Starter',
            'standard'     => 'Standard',
            'professional' => 'Professional',
            'enterprise'   => 'Enterprise',
        ];
    }
}
