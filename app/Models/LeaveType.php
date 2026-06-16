<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $fillable = [
        'name', 'code', 'days_per_year', 'max_carry_forward',
        'is_paid', 'color', 'requires_approval', 'is_active',
    ];

    protected $casts = [
        'is_paid'           => 'boolean',
        'requires_approval' => 'boolean',
        'is_active'         => 'boolean',
    ];

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public static function activeOptions(): array
    {
        return static::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();
    }
}
