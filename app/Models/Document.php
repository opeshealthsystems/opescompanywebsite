<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Document extends Model
{
    protected $fillable = [
        'document_template_id', 'type', 'title', 'reference_number',
        'body_rendered', 'issued_by', 'addressee_user_id',
        'addressee_name', 'addressee_email', 'status',
        'requires_signature', 'signature_token', 'signature_token_expires_at',
        'signed_at', 'signed_by_name', 'signed_ip', 'signed_data',
        'valid_until', 'notes',
    ];

    protected $casts = [
        'requires_signature'         => 'boolean',
        'signature_token_expires_at' => 'datetime',
        'signed_at'                  => 'datetime',
        'signed_data'                => 'array',
        'valid_until'                => 'date',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function addressee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'addressee_user_id');
    }

    public static function generateReferenceNumber(string $type): string
    {
        $prefix = DocumentTemplate::referencePrefix($type);
        $year   = now()->year;

        $last = static::where('type', $type)
            ->whereYear('created_at', $year)
            ->orderByDesc('id')
            ->value('reference_number');

        $seq = 1;
        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return "{$prefix}-{$year}-" . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    public function generateSignatureToken(): void
    {
        $this->update([
            'signature_token'            => Str::random(64),
            'signature_token_expires_at' => now()->addDays(30),
            'status'                     => 'pending_signature',
        ]);
    }

    public static function renderTemplate(DocumentTemplate $template, array $variables): string
    {
        $body = $template->body;
        foreach ($variables as $key => $value) {
            $body = str_replace('{{' . $key . '}}', e($value), $body);
        }
        return $body;
    }

    public function isSigned(): bool
    {
        return $this->status === 'signed' && $this->signed_at !== null;
    }

    public function isSigningTokenValid(): bool
    {
        return $this->signature_token !== null
            && $this->signature_token_expires_at !== null
            && $this->signature_token_expires_at->isFuture()
            && $this->status === 'pending_signature';
    }
}
