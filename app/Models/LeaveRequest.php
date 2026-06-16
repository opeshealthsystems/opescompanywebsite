<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id', 'type', 'start_date', 'end_date', 'total_days',
        'reason', 'status', 'approved_by', 'approved_at', 'notes',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'approved_at' => 'datetime',
        'total_days'  => 'decimal:1',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function typeOptions(): array
    {
        return [
            'annual'    => 'Annual Leave',
            'sick'      => 'Sick Leave',
            'unpaid'    => 'Unpaid Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            'other'     => 'Other',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'pending'   => 'Pending',
            'approved'  => 'Approved',
            'rejected'  => 'Rejected',
            'cancelled' => 'Cancelled',
        ];
    }
}
