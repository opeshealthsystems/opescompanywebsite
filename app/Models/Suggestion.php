<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suggestion extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'category', 'body', 'status', 'admin_response', 'responded_by', 'responded_at'];
    protected $casts = ['responded_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public static function categoryOptions(): array
    {
        return [
            'feature_request' => 'Feature Request',
            'improvement'     => 'Improvement',
            'process'         => 'Process',
            'bug'             => 'Bug Report',
            'other'           => 'Other',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'pending'        => 'Pending',
            'under_review'   => 'Under Review',
            'accepted'       => 'Accepted',
            'declined'       => 'Declined',
            'implemented'    => 'Implemented',
        ];
    }
}
