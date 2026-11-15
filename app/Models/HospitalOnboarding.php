<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HospitalOnboarding extends Model
{
    protected $fillable = [
        // Facility (common)
        'facility_name', 'facility_type', 'facility_size', 'country', 'city',
        'address', 'phone', 'email', 'website', 'registration_number',
        // Person completing the form
        'contact_name', 'contact_title', 'contact_phone', 'contact_email', 'completed_by_role',
        // A. Authorisation & consent
        'owner_name', 'owner_title', 'owner_phone', 'owner_email',
        'completer_authorised', 'data_sharing_consent',
        // B. Clinic identity
        'legal_name', 'trading_name', 'registration_date', 'tax_number',
        'licence_number', 'licence_authority', 'gps_location', 'opening_hours', 'working_language',
        // C. Services
        'services', 'departments', 'beds', 'consulting_rooms', 'patients_per_day',
        'insurers', 'mobile_money', 'vat_rate', 'currency',
        // D. Setup
        'devices', 'connectivity_notes', 'email_domain_choice', 'email_domain',
        'current_software', 'training_attendees', 'preferred_start_date',
        // F. Roles
        'system_admin', 'records_viewers', 'approval_authority',
        // G. Documents + patient-data readiness
        'logo_path', 'stamp_path', 'letterhead_path', 'signed_agreement_path',
        'service_price_list_path', 'drug_stock_path', 'lab_test_list_path',
        'pharmacy_licence_path', 'lab_licence_path',
        'patient_data_format', 'patient_record_count', 'patient_data_notes',
        // Meta
        'notes', 'locale', 'status', 'admin_notes', 'ip_address',
    ];

    protected $casts = [
        'services'             => 'array',
        'preferred_start_date' => 'date',
        'registration_date'    => 'date',
        'completer_authorised' => 'boolean',
        'data_sharing_consent' => 'boolean',
        'beds'                 => 'integer',
        'consulting_rooms'     => 'integer',
        'patients_per_day'     => 'integer',
        'patient_record_count' => 'integer',
    ];

    public function staff(): HasMany
    {
        return $this->hasMany(OnboardingStaff::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(OnboardingSite::class);
    }

    /** Facility type/size reuse the Demo Request vocabulary for consistency. */
    public static function facilityTypes(): array
    {
        return DemoRequest::$institutionTypes;
    }

    public static function sizes(): array
    {
        return DemoRequest::$sizes;
    }

    public static array $services = [
        'consultation' => ['en' => 'Consultation', 'fr' => 'Consultation'],
        'laboratory'   => ['en' => 'Laboratory', 'fr' => 'Laboratoire'],
        'pharmacy'     => ['en' => 'Pharmacy', 'fr' => 'Pharmacie'],
        'maternity'    => ['en' => 'Maternity', 'fr' => 'Maternité'],
        'inpatient'    => ['en' => 'Inpatient', 'fr' => 'Hospitalisation'],
        'imaging'      => ['en' => 'Imaging', 'fr' => 'Imagerie'],
        'dental'       => ['en' => 'Dental', 'fr' => 'Dentaire'],
        'other'        => ['en' => 'Other', 'fr' => 'Autre'],
    ];

    public static array $workingLanguages = [
        'en' => ['en' => 'English', 'fr' => 'Anglais'],
        'fr' => ['en' => 'French', 'fr' => 'Français'],
    ];

    public static array $emailDomainChoices = [
        'own'  => ['en' => 'We have our own email domain', 'fr' => 'Nous avons notre propre domaine email'],
        'ours' => ['en' => 'Please create addresses for us', 'fr' => 'Créez des adresses pour nous'],
    ];

    public static array $patientDataFormats = [
        'excel' => ['en' => 'Excel', 'fr' => 'Excel'],
        'pdf'   => ['en' => 'PDF', 'fr' => 'PDF'],
        'paper' => ['en' => 'Paper only', 'fr' => 'Papier uniquement'],
        'none'  => ['en' => 'No existing records', 'fr' => 'Aucun dossier existant'],
    ];

    public static array $statuses = ['new', 'reviewed', 'accounts_created', 'completed'];

    public static array $statusLabels = [
        'new'              => ['en' => 'New', 'fr' => 'Nouveau'],
        'reviewed'         => ['en' => 'Reviewed', 'fr' => 'Examiné'],
        'accounts_created' => ['en' => 'EMR accounts created', 'fr' => 'Comptes EMR créés'],
        'completed'        => ['en' => 'Completed', 'fr' => 'Terminé'],
    ];
}
