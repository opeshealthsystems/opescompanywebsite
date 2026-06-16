<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReview extends Model
{
    protected $fillable = ['user_id','reviewer_id','review_period','review_date','overall_rating','goals_rating','teamwork_rating','technical_rating','communication_rating','strengths','areas_for_improvement','goals_for_next_period','employee_comments','status','acknowledged_at'];
    protected $casts = ['review_date'=>'date','acknowledged_at'=>'datetime'];

    public function employee(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewer_id'); }

    public static function statusOptions(): array
    {
        return ['draft'=>'Draft','submitted'=>'Submitted','acknowledged'=>'Acknowledged'];
    }

    public static function ratingOptions(): array
    {
        return [1=>'1 - Poor',2=>'2 - Below Average',3=>'3 - Average',4=>'4 - Good',5=>'5 - Excellent'];
    }

    public function averageRating(): float
    {
        return round(($this->overall_rating + $this->goals_rating + $this->teamwork_rating + $this->technical_rating + $this->communication_rating) / 5, 1);
    }
}
