<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'author_name',
        'author_title',
        'author_facility',
        'author_country',
        'body',
        'body_fr',
        'rating',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating'    => 'integer',
    ];
}
