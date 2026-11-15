<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HospitalOnboarding extends Model
{
    protected $fillable = [
        'facility_name', 'facility_type', 'facility_size', 'country', 'city',
        'address', 'phone', 'email', 'website', 'registration_number',
        'contact_name', 'contact_title', 'contact_phone', 'contact_email',
        'products', 'departments', 'preferred_start_date', 'notes',
        'locale', 'status', 'admin_notes', 'ip_address',
    ];

    protected $casts = [
        'products'             => 'array',
        'preferred_start_date' => 'date',
    ];

    public function staff(): HasMany
    {
        return $this->hasMany(OnboardingStaff::class);
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

    public static array $statuses = ['new', 'reviewed', 'accounts_created', 'completed'];

    public static array $statusLabels = [
        'new'              => ['en' => 'New', 'fr' => 'Nouveau'],
        'reviewed'         => ['en' => 'Reviewed', 'fr' => 'Examiné'],
        'accounts_created' => ['en' => 'EMR accounts created', 'fr' => 'Comptes EMR créés'],
        'completed'        => ['en' => 'Completed', 'fr' => 'Terminé'],
    ];
}
