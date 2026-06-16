<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Risk extends Model
{
    protected $fillable = ['reference','title','description','category','likelihood','impact','risk_score','status','owner_id','mitigation_plan','review_date'];
    protected $casts = ['review_date'=>'date'];

    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }

    public static function generateReference(): string
    {
        $year = now()->year;
        $last = static::where('reference', 'like', "RISK-{$year}-%")->orderByDesc('reference')->value('reference');
        $next = $last ? (int) preg_replace('/.*-/', '', $last) + 1 : 1;
        return "RISK-{$year}-" . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public static function categoryOptions(): array
    {
        return ['operational'=>'Operational','financial'=>'Financial','technical'=>'Technical','legal'=>'Legal','strategic'=>'Strategic','reputational'=>'Reputational'];
    }

    public static function likelihoodOptions(): array
    {
        return ['very_low'=>'Very Low (1)','low'=>'Low (2)','medium'=>'Medium (3)','high'=>'High (4)','very_high'=>'Very High (5)'];
    }

    public static function impactOptions(): array
    {
        return ['very_low'=>'Very Low (1)','low'=>'Low (2)','medium'=>'Medium (3)','high'=>'High (4)','very_high'=>'Very High (5)'];
    }

    public static function statusOptions(): array
    {
        return ['open'=>'Open','mitigated'=>'Mitigated','accepted'=>'Accepted','closed'=>'Closed'];
    }

    public static function likelihoodValue(string $level): int
    {
        return match($level) { 'very_low'=>1,'low'=>2,'medium'=>3,'high'=>4,'very_high'=>5, default=>3 };
    }

    public static function computeScore(string $likelihood, string $impact): int
    {
        return static::likelihoodValue($likelihood) * static::likelihoodValue($impact);
    }

    public function scoreColor(): string
    {
        return match(true) {
            $this->risk_score >= 20 => 'danger',
            $this->risk_score >= 12 => 'warning',
            $this->risk_score >= 6  => 'info',
            default                 => 'success',
        };
    }
}
