<?php

namespace App\Filament\Pages;

use App\Support\ValidationMetrics;
use Filament\Pages\Page;

class ValidationDeveloperDashboard extends Page
{
    protected static ?string $title           = 'Developer Throughput';
    protected static ?string $navigationIcon  = 'heroicon-o-wrench';
    protected static ?string $navigationLabel = 'Developer Throughput';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 12;
    protected static string  $view            = 'filament.pages.validation-developer-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getThroughput(): array
    {
        return app(ValidationMetrics::class)->developerThroughput();
    }
}
