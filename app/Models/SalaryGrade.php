<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryGrade extends Model
{
    protected $fillable = ['name', 'code', 'description', 'min_salary', 'max_salary', 'currency', 'is_active'];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(User::class, 'salary_grade_id');
    }

    public static function activeOptions(): array
    {
        return static::where('is_active', true)->orderBy('code')
            ->get()
            ->mapWithKeys(fn ($g) => [
                $g->id => $g->code . ' — ' . $g->name . ' (' . $g->currency . ' ' . number_format((float) $g->min_salary, 0) . ' – ' . number_format((float) $g->max_salary, 0) . ')',
            ])
            ->toArray();
    }
}
