<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PractitionerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','profession','specialty','workplace_name','workplace_city',
        'workplace_country','registration_number','years_of_experience',
        'bio','opes_testimonial','is_verified','payout_number',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'years_of_experience' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function professionOptions(): array
    {
        return [
            'doctor'       => 'Doctor / Physician',
            'nurse'        => 'Nurse',
            'radiologist'  => 'Radiologist',
            'cardiologist' => 'Cardiologist',
            'pharmacist'   => 'Pharmacist',
            'lab_tech'     => 'Laboratory Technician',
            'health_admin' => 'Health Administrator',
            'researcher'   => 'Researcher',
            'other'        => 'Other',
        ];
    }
}
