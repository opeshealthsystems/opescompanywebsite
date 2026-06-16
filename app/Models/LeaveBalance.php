<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    protected $fillable = ['user_id', 'year', 'type', 'entitled_days', 'used_days'];

    protected $casts = [
        'entitled_days' => 'decimal:1',
        'used_days'     => 'decimal:1',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function remainingDays(): float
    {
        return max(0, (float) $this->entitled_days - (float) $this->used_days);
    }
}
