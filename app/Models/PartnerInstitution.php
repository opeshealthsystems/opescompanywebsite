<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PartnerInstitution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','name_fr','type','country','city','website','logo',
        'description','description_fr','partnership_since',
        'is_featured','is_active','sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public static function typeOptions(): array
    {
        return [
            'university'         => 'University',
            'research_institute' => 'Research Institute',
            'ngo'                => 'NGO',
            'government'         => 'Government Agency',
            'hospital_network'   => 'Hospital Network',
            'other'              => 'Other',
        ];
    }

    public function getLocalizedName(string $locale): string
    {
        return ($locale === 'fr' && $this->name_fr) ? $this->name_fr : $this->name;
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }
}
