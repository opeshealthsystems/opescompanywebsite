<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cohort extends Model
{
    use HasFactory;

    protected $fillable = [
        'practitioner_program_id',
        'name',
        'specialty',
        'description',
        'start_date',
        'end_date',
        'max_members',
        'status',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'max_members' => 'integer',
    ];

    public function practitionerProgram(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PractitionerProgram::class, 'practitioner_program_id');
    }

    public function members(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CohortMember::class);
    }

    public function cohortTestCases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CohortTestCase::class);
    }

    public function testCases(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(ValidationTestCase::class, 'cohort_test_cases')
            ->withPivot('due_date')
            ->withTimestamps();
    }

    public static function statusOptions(): array
    {
        return ['draft' => 'Draft', 'active' => 'Active', 'completed' => 'Completed'];
    }
}
