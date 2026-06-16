<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingRecord extends Model
{
    protected $fillable = ['user_id','title','provider','category','status','start_date','completed_at','expires_at','certificate_path','notes'];
    protected $casts = ['start_date'=>'date','completed_at'=>'date','expires_at'=>'date'];

    public function employee(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }

    public static function categoryOptions(): array
    {
        return ['compliance'=>'Compliance','technical'=>'Technical','soft_skills'=>'Soft Skills','safety'=>'Safety','clinical'=>'Clinical','management'=>'Management','other'=>'Other'];
    }

    public static function statusOptions(): array
    {
        return ['planned'=>'Planned','in_progress'=>'In Progress','completed'=>'Completed','expired'=>'Expired'];
    }

    public function isExpiringSoon(): bool
    {
        return $this->expires_at && $this->expires_at->isFuture() && $this->expires_at->diffInDays(now()) <= 30;
    }
}
