<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name',
        'company_email',
        'company_phone',
        'company_address',
        'company_website',
        'default_currency',
        'fiscal_year_start_month',
        'invoice_prefix',
        'quote_prefix',
        'default_tax_rate',
        'timezone',
        'date_format',
        'logo_path',
    ];

    protected $casts = ['default_tax_rate' => 'decimal:2'];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], ['company_name' => 'OPES Health Systems']);
    }

    public static function timezoneOptions(): array
    {
        return [
            'Africa/Douala'    => 'Africa/Douala (WAT +01:00)',
            'Africa/Lagos'     => 'Africa/Lagos (WAT +01:00)',
            'Africa/Nairobi'   => 'Africa/Nairobi (EAT +03:00)',
            'Africa/Accra'     => 'Africa/Accra (GMT +00:00)',
            'Europe/London'    => 'Europe/London (GMT/BST)',
            'Europe/Paris'     => 'Europe/Paris (CET +01:00)',
            'America/New_York' => 'America/New_York (EST -05:00)',
            'UTC'              => 'UTC',
        ];
    }

    public static function monthOptions(): array
    {
        return array_combine(
            range(1, 12),
            array_map(fn($m) => date('F', mktime(0, 0, 0, $m, 1)), range(1, 12))
        );
    }
}
