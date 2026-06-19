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
}
