<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingSite extends Model
{
    protected $fillable = ['hospital_onboarding_id', 'name', 'address'];

    public function onboarding(): BelongsTo
    {
        return $this->belongsTo(HospitalOnboarding::class, 'hospital_onboarding_id');
    }
}
