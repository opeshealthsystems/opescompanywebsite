<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeProfile extends Model
{
    protected $fillable = [
        'user_id', 'employment_type', 'contract_end_date', 'salary', 'currency',
        'bank_name', 'bank_account', 'tax_id',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
        'notes',
    ];

    protected $casts = [
        'contract_end_date' => 'date',
        'salary'            => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function employmentTypeOptions(): array
    {
        return [
            'full_time' => 'Full-Time',
            'part_time' => 'Part-Time',
            'contract'  => 'Contract',
            'intern'    => 'Intern',
        ];
    }
}
