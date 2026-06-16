<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'reference', 'title', 'description', 'category', 'amount', 'currency',
        'vendor', 'expense_date', 'receipt_path', 'status',
        'submitted_by', 'approved_by', 'approved_at', 'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'approved_at'  => 'datetime',
        'amount'       => 'decimal:2',
    ];

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function categoryOptions(): array
    {
        return [
            'payroll'   => 'Payroll',
            'rent'      => 'Rent & Facilities',
            'utilities' => 'Utilities',
            'software'  => 'Software & Subscriptions',
            'hardware'  => 'Hardware & Equipment',
            'travel'    => 'Travel & Transport',
            'marketing' => 'Marketing',
            'legal'     => 'Legal & Compliance',
            'training'  => 'Training & Development',
            'other'     => 'Other',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'pending'  => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'paid'     => 'Paid',
        ];
    }

    public static function generateReference(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->max('reference');
        $seq  = 1;
        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }
        return 'EXP-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    public function formatAmount(): string
    {
        return $this->currency . ' ' . number_format((float) $this->amount, 0);
    }
}
