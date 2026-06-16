<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title', 'body', 'audience', 'is_pinned', 'is_active', 'author_id', 'published_at',
    ];

    protected $casts = [
        'is_pinned'    => 'boolean',
        'is_active'    => 'boolean',
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public static function audienceOptions(): array
    {
        return [
            'all'     => 'All Staff',
            'admin'   => 'Admins Only',
            'support' => 'Support Team',
            'tester'  => 'Testers',
        ];
    }
}
