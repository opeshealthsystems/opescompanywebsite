<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    protected $fillable = [
        'user_id', 'assigned_to',
        'subject', 'description', 'type', 'status', 'priority',
        'resolution', 'resolved_at', 'closed_at',
        'tester_assignment_id',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at'   => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->reference_number)) {
                $ticket->reference_number = static::generateReferenceNumber();
            }
        });
    }

    public static function generateReferenceNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->year;
            $last = static::whereYear('created_at', $year)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('reference_number');
            $seq = 1;
            if ($last && preg_match('/(\d+)$/', $last, $m)) {
                $seq = (int) $m[1] + 1;
            }
            return 'TKT-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at');
    }

    public function publicReplies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->where('is_internal', false)->orderBy('created_at');
    }

    public static function typeLabel(string $type): string
    {
        return static::typeOptions()[$type] ?? ucfirst($type);
    }

    public static function typeOptions(): array
    {
        return [
            'support'    => 'Support',
            'billing'    => 'Billing',
            'technical'  => 'Technical',
            'bug_report' => 'Bug Report',
            'other'      => 'Other',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'open'             => 'Open',
            'in_progress'      => 'In Progress',
            'pending_customer' => 'Pending Customer',
            'resolved'         => 'Resolved',
            'closed'           => 'Closed',
        ];
    }

    public static function priorityOptions(): array
    {
        return [
            'low'    => 'Low',
            'medium' => 'Medium',
            'high'   => 'High',
            'urgent' => 'Urgent',
        ];
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'in_progress', 'pending_customer']);
    }

    public function testerAssignment(): BelongsTo
    {
        return $this->belongsTo(TesterAssignment::class);
    }
}
