<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = ['year', 'category', 'department', 'allocated_amount', 'currency', 'notes'];

    protected $casts = ['allocated_amount' => 'decimal:2'];

    public static function categoryOptions(): array
    {
        return Expense::categoryOptions();
    }
}
