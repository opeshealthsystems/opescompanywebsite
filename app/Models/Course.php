<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'product_slug','title','title_fr','slug','description','description_fr',
        'level','duration_hours','cover_image','is_active','is_featured','sort_order',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function lessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class)->orderBy('sort_order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(CourseCertificate::class);
    }

    public static function levelOptions(): array
    {
        return [
            'beginner'     => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced'     => 'Advanced',
        ];
    }

    public function getLocalizedTitle(string $locale): string
    {
        return ($locale === 'fr' && $this->title_fr) ? $this->title_fr : $this->title;
    }
}
