<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TesterAssignment extends Model
{
    protected $fillable = [
        'assigned_to', 'assigned_by', 'product_slug', 'product_name',
        'title', 'description', 'status', 'due_date', 'notes',
    ];

    protected $casts = [
        'due_date'    => 'date',
        'assigned_to' => 'integer',
    ];

    public function tester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function bugReports(): HasMany
    {
        return $this->hasMany(Ticket::class, 'tester_assignment_id');
    }

    public static function statusOptions(): array
    {
        return [
            'pending'     => 'Pending',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled',
        ];
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'in_progress']);
    }

    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isPast()
            && $this->isActive();
    }
}
