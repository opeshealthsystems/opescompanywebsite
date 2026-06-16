<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PractitionerBugReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'practitioner_id',
        'product_slug',
        'title',
        'severity',
        'description',
        'steps_to_reproduce',
        'screenshot_url',
        'status',
        'admin_response',
        'responded_by',
        'responded_at',
    ];

    protected $casts = ['responded_at' => 'datetime'];

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'practitioner_id');
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public static function severityOptions(): array
    {
        return [
            'low'      => 'Low',
            'medium'   => 'Medium',
            'high'     => 'High',
            'critical' => 'Critical',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'open'        => 'Open',
            'triaged'     => 'Triaged',
            'in_progress' => 'In Progress',
            'resolved'    => 'Resolved',
            'closed'      => 'Closed',
            'wont_fix'    => "Won't Fix",
        ];
    }
}
