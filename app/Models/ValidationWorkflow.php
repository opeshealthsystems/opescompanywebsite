<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationWorkflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'validation_module_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function module(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ValidationModule::class, 'validation_module_id');
    }

    public function testCases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ValidationTestCase::class);
    }
}
