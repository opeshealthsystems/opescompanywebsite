<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title', 'title_fr', 'slug', 'excerpt', 'excerpt_fr',
        'body', 'body_fr', 'cover_image', 'category',
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
}
