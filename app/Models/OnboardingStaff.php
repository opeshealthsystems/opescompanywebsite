<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingStaff extends Model
{
    protected $table = 'onboarding_staff';

    protected $fillable = [
        'hospital_onboarding_id', 'full_name', 'job_role', 'specialty', 'department', 'site',
        'phone', 'email', 'access_level', 'takes_tasks',
        'licence_number', 'licence_issuing_body', 'licence_expiry', 'years_experience',
        'working_hours', 'consultation_fee', 'signature_path',
        'emr_account_created', 'emr_username', 'notes',
    ];

    protected $casts = [
        'takes_tasks'         => 'boolean',
        'emr_account_created' => 'boolean',
        'licence_expiry'      => 'date',
        'years_experience'    => 'integer',
    ];

    public function onboarding(): BelongsTo
    {
        return $this->belongsTo(HospitalOnboarding::class, 'hospital_onboarding_id');
    }

    /** Profession / job title of the staff member. */
    public static array $jobRoles = [
        'doctor'          => ['en' => 'Doctor / Physician', 'fr' => 'Médecin'],
        'nurse'           => ['en' => 'Nurse', 'fr' => 'Infirmier(ère)'],
        'lab'             => ['en' => 'Lab', 'fr' => 'Laboratoire'],
        'pharmacy'        => ['en' => 'Pharmacy', 'fr' => 'Pharmacie'],
        'reception'       => ['en' => 'Reception', 'fr' => 'Accueil'],
        'billing'         => ['en' => 'Billing', 'fr' => 'Facturation'],
        'records_officer' => ['en' => 'Medical Records Officer', 'fr' => 'Archiviste médical'],
        'admin'           => ['en' => 'Admin', 'fr' => 'Administration'],
        'it_officer'      => ['en' => 'IT Officer', 'fr' => 'Responsable informatique'],
        'other'           => ['en' => 'Other', 'fr' => 'Autre'],
    ];

    /** Access tiers, per the clinic-setup form: view only / enter data / approve / admin. */
    public static array $accessLevels = [
        'view_only'  => ['en' => 'View only', 'fr' => 'Lecture seule'],
        'enter_data' => ['en' => 'Enter data', 'fr' => 'Saisie de données'],
        'approve'    => ['en' => 'Approve (prescriptions / results)', 'fr' => 'Approuver (ordonnances / résultats)'],
        'admin'      => ['en' => 'Administrator', 'fr' => 'Administrateur'],
    ];
}
