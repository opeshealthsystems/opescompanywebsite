<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDeductionType extends Model
{
    protected $fillable = ['name', 'code', 'calculation_type', 'rate', 'description', 'apply_by_default', 'is_active', 'sort_order'];

    protected $casts = [
        'apply_by_default' => 'boolean',
        'is_active'        => 'boolean',
        'rate'             => 'decimal:4',
    ];

    public function calculateAmount(float $grossSalary): float
    {
        if ($this->calculation_type === 'percentage') {
            return round($grossSalary * ((float) $this->rate / 100), 2);
        }

        return (float) $this->rate;
    }

    public static function defaultDeductions(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)
            ->where('apply_by_default', true)
            ->orderBy('sort_order')
            ->get();
    }
}
