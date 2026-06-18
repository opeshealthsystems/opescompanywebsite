<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TesterProfile extends Model
{
    protected $fillable = [
        'user_id', 'testing_specialty', 'device_types',
        'portfolio_url', 'certifications', 'availability_notes', 'bio',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function specialtyOptions(): array
    {
        return ['web' => 'Web', 'mobile' => 'Mobile', 'api' => 'API', 'desktop' => 'Desktop'];
    }
}
