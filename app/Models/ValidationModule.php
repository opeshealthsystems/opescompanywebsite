<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'validation_product_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ValidationProduct::class, 'validation_product_id');
    }

    public function workflows(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ValidationWorkflow::class);
    }
}
