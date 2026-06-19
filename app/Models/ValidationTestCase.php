<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationTestCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'validation_workflow_id',
        'title',
        'description',
        'steps',
        'expected_result',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function workflow(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ValidationWorkflow::class, 'validation_workflow_id');
    }
}
