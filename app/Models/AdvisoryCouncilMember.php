<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvisoryCouncilMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'validation_certificate_id', 'title',
        'term_start', 'term_end', 'status', 'invited_by', 'invited_at',
    ];

    protected $casts = [
        'term_start' => 'date',
        'term_end'   => 'date',
        'invited_at' => 'datetime',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function validationCertificate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ValidationCertificate::class);
    }

    public function invitedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public static function statusOptions(): array
    {
        return ['active' => 'Active', 'inactive' => 'Inactive'];
    }
}
