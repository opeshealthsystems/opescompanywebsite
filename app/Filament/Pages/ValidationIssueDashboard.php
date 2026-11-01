<?php

namespace App\Filament\Pages;

use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use Filament\Pages\Page;

class ValidationIssueDashboard extends Page
{
    protected static ?string $title           = 'Issue Analytics';
    protected static ?string $navigationIcon  = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'Issue Analytics';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 11;
    protected static string  $view            = 'filament.pages.validation-issue-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getAnalytics(): array
    {
        return AdminWorkflowAdapter::read('admin.validation.metrics.issueAnalytics')['analytics'];
    }
}
