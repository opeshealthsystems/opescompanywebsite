<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingStaff extends Model
{
    protected $table = 'onboarding_staff';

    protected $fillable = [
        'hospital_onboarding_id', 'full_name', 'job_role', 'department',
        'phone', 'email', 'access_level', 'takes_tasks',
        'emr_account_created', 'emr_username', 'notes',
    ];

    protected $casts = [
        'takes_tasks'         => 'boolean',
        'emr_account_created' => 'boolean',
    ];

    public function onboarding(): BelongsTo
    {
        return $this->belongsTo(HospitalOnboarding::class, 'hospital_onboarding_id');
    }

    /** Profession / job title of the staff member. */
    public static array $jobRoles = [
        'doctor'          => ['en' => 'Doctor / Physician', 'fr' => 'Médecin'],
        'nurse'           => ['en' => 'Nurse', 'fr' => 'Infirmier(ère)'],
        'lab_technician'  => ['en' => 'Lab Technician', 'fr' => 'Technicien(ne) de laboratoire'],
        'pharmacist'      => ['en' => 'Pharmacist', 'fr' => 'Pharmacien(ne)'],
        'receptionist'    => ['en' => 'Receptionist / Front Desk', 'fr' => 'Réceptionniste / Accueil'],
        'records_officer' => ['en' => 'Medical Records Officer', 'fr' => 'Archiviste médical'],
        'cashier'         => ['en' => 'Cashier / Billing', 'fr' => 'Caissier(ère) / Facturation'],
        'administrator'   => ['en' => 'Administrator / Manager', 'fr' => 'Administrateur / Gestionnaire'],
        'it_officer'      => ['en' => 'IT Officer', 'fr' => 'Responsable informatique'],
        'other'           => ['en' => 'Other', 'fr' => 'Autre'],
    ];

    /** Intended level of access inside the EMR. */
    public static array $accessLevels = [
        'administrator' => ['en' => 'Administrator (full access)', 'fr' => 'Administrateur (accès complet)'],
        'clinician'     => ['en' => 'Clinician (clinical records)', 'fr' => 'Clinicien (dossiers cliniques)'],
        'front_desk'    => ['en' => 'Front desk / Operations', 'fr' => 'Accueil / Opérations'],
        'read_only'     => ['en' => 'Read only', 'fr' => 'Lecture seule'],
    ];
}
