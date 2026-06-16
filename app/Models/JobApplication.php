<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    protected $fillable = ['job_opening_id','applicant_name','email','phone','status','resume_path','applied_at','interview_date','notes'];
    protected $casts = ['applied_at'=>'date','interview_date'=>'date'];

    public function jobOpening(): BelongsTo { return $this->belongsTo(JobOpening::class); }

    public static function statusOptions(): array
    {
        return ['received'=>'Received','reviewing'=>'Reviewing','shortlisted'=>'Shortlisted','interviewed'=>'Interviewed','offered'=>'Offered','hired'=>'Hired','rejected'=>'Rejected'];
    }
}
