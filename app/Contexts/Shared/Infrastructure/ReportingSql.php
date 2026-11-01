<?php

namespace App\Contexts\Shared\Infrastructure;

use Illuminate\Support\Facades\DB;

final class ReportingSql
{
    public static function month(string $column): string
    {
        if (!preg_match('/\A[a-z_]+(?:\.[a-z_]+)?\z/', $column)) throw new \InvalidArgumentException('Invalid report column');
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', $column)",
            'pgsql' => "to_char($column, 'YYYY-MM')",
            default => "DATE_FORMAT($column, '%Y-%m')",
        };
    }
}
