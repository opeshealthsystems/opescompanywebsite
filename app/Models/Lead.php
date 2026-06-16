<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'facility_type', 'products',
        'message', 'source', 'product_slug', 'locale', 'status', 'ip_address',
    ];

    public function quotes(): HasMany
    {
        return $this->hasMany(\App\Models\Quote::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(\App\Models\Deal::class);
    }
}
