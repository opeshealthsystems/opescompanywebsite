<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;
use App\Enums\PractitionerTier;
use App\Models\Department;
use App\Models\PerformanceReview;
use App\Models\Timesheet;
use App\Models\TrainingRecord;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'phone',
        'department',
        'position',
        'hire_date',
        'is_active',
        'avatar',
        'department_id',
        'salary_grade_id',
        'base_salary',
        'salary_currency',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin', 'support']);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'hire_date'         => 'date',
            'is_active'         => 'boolean',
        ];
    }

    public function customerProfile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function employeeProfile(): HasOne
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function payrollEntries(): HasMany
    {
        return $this->hasMany(PayrollEntry::class);
    }

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function salaryGrade(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\SalaryGrade::class);
    }

    public function performanceReviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class, 'user_id');
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class, 'user_id');
    }

    public function trainingRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TrainingRecord::class, 'user_id');
    }

    public function practitionerProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PractitionerProfile::class);
    }

    public function testerProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\TesterProfile::class);
    }

    public function managerProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\ManagerProfile::class);
    }

    public function supportProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\SupportProfile::class);
    }

    public function practitionerFindings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\PractitionerFinding::class, 'practitioner_id');
    }

    public function practitionerTier(): PractitionerTier
    {
        $isVerified = (bool) $this->practitionerProfile?->is_verified;
        $publishedFindings = $this->practitionerFindings()->where('is_published', true)->count();

        return PractitionerTier::forProfile($isVerified, $publishedFindings);
    }

    public function practitionerApplications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\PractitionerApplication::class, 'practitioner_id');
    }

    public function isVerifiedPractitioner(): bool
    {
        return $this->hasRole('practitioner') && (bool) ($this->practitionerProfile?->is_verified);
    }

    public function surveyResponses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\SurveyResponse::class);
    }

    public function suggestions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Suggestion::class);
    }

    public function practitionerBugReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\PractitionerBugReport::class, 'practitioner_id');
    }

    public function serviceRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\ServiceRequest::class, 'customer_id');
    }

    public function courseEnrollments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\CourseEnrollment::class);
    }

    public function courseCertificates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\CourseCertificate::class);
    }
}
