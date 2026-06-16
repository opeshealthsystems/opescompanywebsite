<?php

namespace App\Models;

use App\Traits\LogsAuditActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deal extends Model
{
    use LogsAuditActivity;
    protected $fillable = [
        'reference', 'title', 'lead_id', 'stage', 'value', 'currency',
        'probability', 'expected_close_date', 'actual_close_date',
        'owner_id', 'notes', 'lost_reason',
    ];

    protected $casts = [
        'value'               => 'decimal:2',
        'expected_close_date' => 'date',
        'actual_close_date'   => 'date',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public static function generateReference(): string
    {
        $year = now()->year;
        $last = static::where('reference', 'like', "DEAL-{$year}-%")->orderByDesc('reference')->value('reference');
        $next = $last ? (int) preg_replace('/.*-/', '', $last) + 1 : 1;
        return "DEAL-{$year}-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public static function stageOptions(): array
    {
        return [
            'prospecting'  => 'Prospecting',
            'qualification' => 'Qualification',
            'proposal'     => 'Proposal',
            'negotiation'  => 'Negotiation',
            'closed_won'   => 'Closed Won',
            'closed_lost'  => 'Closed Lost',
        ];
    }

    public function weightedValue(): float
    {
        return (float) $this->value * ($this->probability / 100);
    }

    public function formatValue(): string
    {
        return $this->currency . ' ' . number_format((float) $this->value, 0);
    }
}
