<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = ['reference','title','description','status','priority','owner_id','start_date','end_date','budget','currency','notes'];
    protected $casts = ['start_date'=>'date','end_date'=>'date','budget'=>'decimal:2'];

    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }
    public function milestones(): HasMany { return $this->hasMany(Milestone::class); }
    public function timesheets(): HasMany { return $this->hasMany(Timesheet::class); }

    public static function generateReference(): string
    {
        return \App\Contexts\Operations\Application\ProjectReference::next();
    }

    public static function statusOptions(): array
    {
        return ['planning'=>'Planning','active'=>'Active','on_hold'=>'On Hold','completed'=>'Completed','cancelled'=>'Cancelled'];
    }

    public static function priorityOptions(): array
    {
        return ['low'=>'Low','medium'=>'Medium','high'=>'High','critical'=>'Critical'];
    }
}
