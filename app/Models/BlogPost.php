<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogPost extends Model
{
    protected $fillable = [
        'title', 'title_fr', 'slug', 'excerpt', 'excerpt_fr',
        'body', 'body_fr', 'cover_image', 'reading_time', 'category',
        'author', 'published', 'published_at',
    ];

    protected $casts = [
        'published'    => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('published', true)->orderByDesc('published_at');
    }

    public function getLocalizedTitle(string $locale): string
    {
        return ($locale === 'fr' && $this->title_fr) ? $this->title_fr : $this->title;
    }

    public function getLocalizedExcerpt(string $locale): string
    {
        return ($locale === 'fr' && $this->excerpt_fr) ? $this->excerpt_fr : ($this->excerpt ?? '');
    }

    public static function calculateReadingTime(string $htmlBody): int
    {
        $words = str_word_count(strip_tags($htmlBody));
        return (int) max(1, ceil($words / 220));
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }
}
