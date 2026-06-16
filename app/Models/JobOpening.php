<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOpening extends Model
{
    protected $fillable = ['title','department_id','type','location','status','description','requirements','openings_count','posted_at','closes_at','salary_range','created_by'];
    protected $casts = ['posted_at'=>'date','closes_at'=>'date'];

    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function applications(): HasMany { return $this->hasMany(JobApplication::class); }

    public static function typeOptions(): array
    {
        return ['full_time'=>'Full Time','part_time'=>'Part Time','contract'=>'Contract','internship'=>'Internship','remote'=>'Remote'];
    }

    public static function statusOptions(): array
    {
        return ['open'=>'Open','paused'=>'Paused','closed'=>'Closed','filled'=>'Filled'];
    }

    public function applicationsCount(): int { return $this->applications()->count(); }
}
