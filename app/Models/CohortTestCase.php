<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CohortTestCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_id',
        'validation_test_case_id',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function cohort(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Cohort::class, 'cohort_id');
    }

    public function testCase(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ValidationTestCase::class, 'validation_test_case_id');
    }
}
