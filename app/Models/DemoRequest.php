<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoRequest extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'organization_name', 'country',
        'institution_type', 'institution_size', 'products', 'message',
        'preferred_date', 'locale', 'status', 'admin_notes', 'ip_address',
    ];

    protected $casts = [
        'products'       => 'array',
        'preferred_date' => 'date',
    ];

    public static array $institutionTypes = [
        'government_hospital' => ['en' => 'Government Hospital', 'fr' => 'Hôpital public'],
        'private_hospital'    => ['en' => 'Private Hospital', 'fr' => 'Hôpital privé'],
        'clinic'              => ['en' => 'Clinic / Health Centre', 'fr' => 'Clinique / Centre de santé'],
        'pharmacy'            => ['en' => 'Pharmacy', 'fr' => 'Pharmacie'],
        'laboratory'          => ['en' => 'Diagnostic Laboratory', 'fr' => 'Laboratoire'],
        'ministry'            => ['en' => 'Ministry / Government Agency', 'fr' => 'Ministère / Agence gouvernementale'],
        'ngo'                 => ['en' => 'NGO / International Organisation', 'fr' => 'ONG / Organisation internationale'],
        'other'               => ['en' => 'Other', 'fr' => 'Autre'],
    ];

    public static array $sizes = ['1-10', '11-50', '51-200', '201-500', '500+'];

    public static array $statuses = ['new', 'contacted', 'scheduled', 'completed', 'rejected'];
}
