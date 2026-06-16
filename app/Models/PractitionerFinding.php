<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PractitionerFinding extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id','practitioner_id','wait_time_rating',
        'data_integrity_rating','usability_rating','overall_rating',
        'findings_text','video_url','is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function application()
    {
        return $this->belongsTo(PractitionerApplication::class);
    }

    public function practitioner()
    {
        return $this->belongsTo(User::class, 'practitioner_id');
    }

    public function averageRating(): float
    {
        $ratings = array_filter([
            $this->wait_time_rating,
            $this->data_integrity_rating,
            $this->usability_rating,
            $this->overall_rating,
        ]);
        return count($ratings) ? array_sum($ratings) / count($ratings) : 0.0;
    }
}
