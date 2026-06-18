<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TesterApplication extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'profession', 'specialty',
        'institution_name', 'country', 'city', 'years_experience',
        'devices', 'platforms', 'motivation', 'tech_experience',
        'locale', 'status', 'admin_notes', 'ip_address',
    ];

    protected $casts = [
        'devices'   => 'array',
        'platforms' => 'array',
    ];

    public static array $professions = [
        'doctor'       => ['en' => 'Doctor / Physician', 'fr' => 'Médecin'],
        'nurse'        => ['en' => 'Nurse', 'fr' => 'Infirmier/ère'],
        'pharmacist'   => ['en' => 'Pharmacist', 'fr' => 'Pharmacien/ne'],
        'lab_tech'     => ['en' => 'Laboratory Technician', 'fr' => 'Technicien de laboratoire'],
        'radiologist'  => ['en' => 'Radiologist / Imaging', 'fr' => 'Radiologue / Imagerie'],
        'health_admin' => ['en' => 'Health Administrator', 'fr' => 'Administrateur de santé'],
        'researcher'   => ['en' => 'Health Researcher', 'fr' => 'Chercheur en santé'],
        'it_health'    => ['en' => 'Health IT Professional', 'fr' => 'Professionnel IT santé'],
        'other'        => ['en' => 'Other', 'fr' => 'Autre'],
    ];

    public static array $statuses = ['pending', 'accepted', 'rejected', 'active'];
}
