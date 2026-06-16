<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timesheet extends Model
{
    protected $fillable = ['user_id','project_id','date','hours','description','is_billable'];
    protected $casts = ['date'=>'date','hours'=>'decimal:2','is_billable'=>'boolean'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
}
