<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** Public cards must resolve to an existing detail page, including before content seeding. */
    public static function publicCatalogue(): \Illuminate\Support\Collection
    {
        $catalogue = array_merge(config('products', []), config('products_specialist', []), config('products_drugtrace', []));
        $stored = static::orderBy('sort_order')->get();
        if ($stored->isNotEmpty()) {
            return $stored->where('is_active', true)->filter(fn ($p) => isset($catalogue[$p->slug]))->values();
        }

        return collect($catalogue)->map(function ($data, $slug) {
            $data['slug'] = $slug;
            $data['category'] = match ($data['category'] ?? '') {
                'Core Platform' => 'core',
                'Diagnostics' => 'diagnostics',
                'Medicine Safety' => 'specialist',
                default => 'specialist',
            };
            return new static($data);
        })->values();
    }

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
