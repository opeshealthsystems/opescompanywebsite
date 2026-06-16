<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    protected $fillable = [
        'reference', 'period_start', 'period_end', 'status',
        'total_gross', 'total_net', 'currency', 'notes',
        'processed_by', 'completed_at',
    ];

    protected $casts = [
        'period_start'  => 'date',
        'period_end'    => 'date',
        'completed_at'  => 'datetime',
        'total_gross'   => 'decimal:2',
        'total_net'     => 'decimal:2',
    ];

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(PayrollEntry::class);
    }

    public static function generateReference(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->max('reference');
        $seq  = 1;
        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }
        return 'PAY-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public static function statusOptions(): array
    {
        return [
            'draft'      => 'Draft',
            'processing' => 'Processing',
            'completed'  => 'Completed',
            'cancelled'  => 'Cancelled',
        ];
    }

    public function recalculateTotals(): void
    {
        $this->total_gross = $this->entries()->sum('gross_salary');
        $this->total_net   = $this->entries()->sum('net_salary');
        $this->save();
    }
}
