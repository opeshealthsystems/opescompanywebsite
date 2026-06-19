<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_report_id', 'assigned_to', 'title', 'priority',
        'status', 'resolution_notes', 'started_at', 'fixed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'fixed_at'   => 'datetime',
    ];

    public function issueReport(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(IssueReport::class);
    }

    public function assignedTo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function retests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Retest::class);
    }

    public static function statusOptions(): array
    {
        return [
            'open'        => 'Open',
            'in_progress' => 'In Progress',
            'fixed'       => 'Fixed',
            'reopened'    => 'Reopened',
            'wont_fix'    => "Won't Fix",
        ];
    }

    public static function priorityOptions(): array
    {
        return IssueReport::severityOptions();
    }

    public function markInProgress(): void
    {
        $this->update([
            'status'     => 'in_progress',
            'started_at' => $this->started_at ?? now(),
        ]);
    }

    public function markFixed(?string $notes = null): void
    {
        $this->update([
            'status'           => 'fixed',
            'fixed_at'         => now(),
            'resolution_notes' => $notes ?? $this->resolution_notes,
        ]);

        // Advance the issue into retest only from a live development state.
        // Never resurrect a terminal issue (closed / retest_passed) back into
        // the loop — closure is a one-way decision.
        $issue = $this->issueReport()->first();
        if ($issue && in_array($issue->status, ['sent_to_development', 'retest_failed'], true)) {
            $issue->update(['status' => 'ready_for_retest']);
        }
    }

    public function reopen(): void
    {
        $this->update(['status' => 'reopened']);
    }

    public function markWontFix(?string $notes = null): void
    {
        $this->update([
            'status'           => 'wont_fix',
            'resolution_notes' => $notes ?? $this->resolution_notes,
        ]);
        $this->issueReport->update(['status' => 'rejected']);
    }
}
