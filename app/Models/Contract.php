<?php

namespace App\Models;

use App\Traits\LogsAuditActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    use LogsAuditActivity;
    protected $fillable = [
        'reference', 'title', 'lead_id', 'type', 'status',
        'value', 'currency', 'start_date', 'end_date',
        'auto_renew', 'signed_at', 'created_by', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'signed_at'  => 'datetime',
        'auto_renew' => 'boolean',
        'value'      => 'decimal:2',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateReference(): string
    {
        $year = now()->year;
        $last = static::where('reference', 'like', "CONT-{$year}-%")
            ->orderByDesc('reference')
            ->value('reference');
        $next = $last ? (int) preg_replace('/.*-/', '', $last) + 1 : 1;
        return "CONT-{$year}-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public static function typeOptions(): array
    {
        return [
            'service_agreement' => 'Service Agreement',
            'nda'               => 'NDA',
            'sla'               => 'SLA',
            'partnership'       => 'Partnership',
            'vendor'            => 'Vendor Contract',
            'employment'        => 'Employment Contract',
            'other'             => 'Other',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'draft'      => 'Draft',
            'sent'       => 'Sent',
            'active'     => 'Active',
            'expired'    => 'Expired',
            'terminated' => 'Terminated',
            'renewed'    => 'Renewed',
        ];
    }

    public function isExpiringSoon(): bool
    {
        return $this->end_date
            && $this->end_date->isFuture()
            && $this->end_date->diffInDays(now()) <= 30;
    }

    public function formatValue(): string
    {
        return $this->currency . ' ' . number_format((float) $this->value, 0);
    }
}
