<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiIdempotencyRecord extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['response' => 'array', 'expires_at' => 'datetime'];
    }
}
