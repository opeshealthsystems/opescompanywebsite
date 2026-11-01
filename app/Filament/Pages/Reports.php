<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;

class Reports extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?string $navigationLabel = 'Overview';
    protected static ?int $navigationSort     = 50;
    protected static string $view             = 'filament.pages.reports';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getViewData(): array
    {
        return app(WorkflowDispatcher::class)->execute('admin.reports.operational.overview',
            new WorkflowInput(actor: auth()->user(), language: app()->getLocale()))->data;
    }
}
