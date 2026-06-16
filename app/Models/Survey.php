<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'title_fr', 'description', 'description_fr',
        'audience', 'status', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('sort_order');
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public static function audienceOptions(): array
    {
        return [
            'practitioners' => 'Practitioners',
            'customers'     => 'Customers',
            'all'           => 'All Users',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'draft'  => 'Draft',
            'active' => 'Active',
            'closed' => 'Closed',
        ];
    }
}
