<?php

namespace App\Contexts\Operations\Application;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

final class ProjectReference
{
    public static function next(): string
    {
        return DB::transaction(function () {
            $prefix = 'PROJ-'.now()->year.'-'; $name = 'project:'.now()->year;
            DB::table('business_sequences')->insertOrIgnore(['name' => $name, 'last_value' => 0]);
            $sequence = DB::table('business_sequences')->where('name', $name)->lockForUpdate()->first();
            $previous = (int) $sequence->last_value;
            if ($previous === 0) $previous = (int) Project::where('reference', 'like', $prefix.'%')->pluck('reference')
                ->map(fn ($reference) => ctype_digit(substr($reference, strlen($prefix))) ? (int) substr($reference, strlen($prefix)) : 0)->max();
            $next = $previous + 1;
            DB::table('business_sequences')->where('name', $name)->update(['last_value' => $next]);
            return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
        }, 3);
    }
}
