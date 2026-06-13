<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentTemplate extends Model
{
    protected $fillable = [
        'name', 'type', 'body', 'variables', 'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'receipt'            => 'Receipt',
            'letterhead'         => 'Letterhead',
            'contract_employee'  => 'Employee Contract',
            'contract_business'  => 'Business Contract',
            default              => ucfirst($type),
        };
    }

    public static function referencePrefix(string $type): string
    {
        return match ($type) {
            'receipt'            => 'RCT',
            'letterhead'         => 'LTH',
            'contract_employee'  => 'EMP-CNT',
            'contract_business'  => 'BSN-CNT',
            default              => 'DOC',
        };
    }
}
