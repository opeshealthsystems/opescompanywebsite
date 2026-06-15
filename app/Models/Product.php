<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'name_fr',
        'subtitle',
        'subtitle_fr',
        'category',
        'tagline',
        'icon',
        'color',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'product_slug', 'slug');
    }

    public function activeLicensesCount(): int
    {
        return $this->licenses()->where('status', 'active')->count();
    }

    public static function categoryLabel(string $category): string
    {
        return match ($category) {
            'core'        => 'Core Platform',
            'diagnostics' => 'Diagnostics',
            'specialist'  => 'Specialist Systems',
            default       => ucfirst($category),
        };
    }

    public static function categoryOptions(): array
    {
        return [
            'core'        => 'Core Platform',
            'diagnostics' => 'Diagnostics',
            'specialist'  => 'Specialist Systems',
        ];
    }
}
