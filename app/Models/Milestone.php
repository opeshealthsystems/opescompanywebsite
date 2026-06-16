<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Milestone extends Model
{
    protected $fillable = ['project_id','title','description','due_date','status','completed_at'];
    protected $casts = ['due_date'=>'date','completed_at'=>'datetime'];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }

    public static function statusOptions(): array
    {
        return ['pending'=>'Pending','in_progress'=>'In Progress','completed'=>'Completed','overdue'=>'Overdue'];
    }
}
