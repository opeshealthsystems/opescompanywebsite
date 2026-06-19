<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_id', 'week_start', 'week_end', 'metrics',
        'summary', 'action_items', 'author_id', 'generated_at',
    ];

    protected $casts = [
        'week_start'   => 'date',
        'week_end'     => 'date',
        'metrics'      => 'array',
        'generated_at' => 'datetime',
    ];

    public function cohort(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
