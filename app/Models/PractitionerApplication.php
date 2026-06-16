<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PractitionerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'practitioner_id','program_id','motivation','status',
        'reviewed_by','reviewed_at','admin_notes',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function practitioner()
    {
        return $this->belongsTo(User::class, 'practitioner_id');
    }

    public function program()
    {
        return $this->belongsTo(PractitionerProgram::class, 'program_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function findings()
    {
        return $this->hasMany(PractitionerFinding::class, 'application_id');
    }

    public static function statusOptions(): array
    {
        return [
            'pending'   => 'Pending',
            'approved'  => 'Approved',
            'rejected'  => 'Rejected',
            'withdrawn' => 'Withdrawn',
        ];
    }
}
