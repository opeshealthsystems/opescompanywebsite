<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteFormDelivery extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['locked_at' => 'datetime', 'next_attempt_at' => 'datetime', 'sent_at' => 'datetime'];
    }
}
