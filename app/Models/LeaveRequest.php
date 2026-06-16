<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id', 'leave_type_id', 'type', 'start_date', 'end_date', 'total_days',
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

    public function leaveType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
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

    public function getDurationInDays(): int
    {
        if (!$this->start_date || !$this->end_date) return 0;
        $start = \Carbon\Carbon::parse($this->start_date);
        $end   = \Carbon\Carbon::parse($this->end_date);
        return max(1, (int) $start->diffInDays($end) + 1);
    }

    public function deductFromBalance(): void
    {
        // Prefer the stored total_days; fall back to date calculation
        $days = (int) ($this->total_days > 0 ? $this->total_days : $this->getDurationInDays());
        if ($days <= 0) return;

        $userId = $this->user_id ?? null;
        if (!$userId) return;

        $year = \Carbon\Carbon::parse($this->start_date)->year;

        // LeaveBalance.type matches LeaveRequest.type (string key e.g. 'annual', 'sick')
        $balance = \App\Models\LeaveBalance::where('user_id', $userId)
            ->where('year', $year)
            ->where('type', $this->type)
            ->first();

        if ($balance) {
            $balance->increment('used_days', $days);
        }
    }
}
