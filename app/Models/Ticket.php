<?php

namespace App\Models;

use App\Models\SlaRule;
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
        'sla_rule_id', 'sla_response_due_at', 'sla_resolution_due_at',
    ];

    protected $casts = [
        'user_id'               => 'integer',
        'resolved_at'           => 'datetime',
        'closed_at'             => 'datetime',
        'sla_response_due_at'   => 'datetime',
        'sla_resolution_due_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->reference_number)) {
                $ticket->reference_number = static::generateReferenceNumber();
            }
        });

        static::updating(function (Ticket $ticket) {
            if ($ticket->isDirty('status')) {
                if ($ticket->status === 'resolved' && !$ticket->resolved_at) {
                    $ticket->resolved_at = now();
                }
                if ($ticket->status === 'closed' && !$ticket->closed_at) {
                    $ticket->closed_at = now();
                }
            }
        });

        static::created(function (Ticket $ticket) {
            $rule = SlaRule::forTicket($ticket->type ?? '', $ticket->priority ?? 'low');
            if ($rule) {
                $ticket->update([
                    'sla_rule_id'           => $rule->id,
                    'sla_response_due_at'   => $ticket->created_at->addHours($rule->response_time_hours),
                    'sla_resolution_due_at' => $ticket->created_at->addHours($rule->resolution_time_hours),
                ]);
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

    public function slaRule(): BelongsTo
    {
        return $this->belongsTo(SlaRule::class);
    }

    public function isSlaResponseBreached(): bool
    {
        return $this->sla_response_due_at && now()->isAfter($this->sla_response_due_at) && !in_array($this->status, ['resolved','closed']);
    }

    public function isSlaResolutionBreached(): bool
    {
        return $this->sla_resolution_due_at && now()->isAfter($this->sla_resolution_due_at) && !in_array($this->status, ['resolved','closed']);
    }
}
