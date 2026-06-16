<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollEntry extends Model
{
    protected $fillable = [
        'payroll_run_id', 'user_id', 'gross_salary',
        'deductions', 'total_deductions', 'net_salary', 'currency', 'status',
    ];

    protected $casts = [
        'deductions'       => 'array',
        'gross_salary'     => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary'       => 'decimal:2',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function formatNet(): string
    {
        return $this->currency . ' ' . number_format((float) $this->net_salary, 0);
    }
}
