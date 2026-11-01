<?php

namespace App\Filament\Pages;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use Filament\Pages\Page;

class ReportsDashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reports';
    protected static ?string $title           = 'Reports Dashboard';
    protected static ?string $slug            = 'reports-dashboard';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int    $navigationSort  = 51;
    protected static string  $view            = 'filament.pages.reports-dashboard';

    public array $metrics = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function mount(): void
    {
        $result = app(WorkflowDispatcher::class)->execute('admin.reports.dashboard',
            new WorkflowInput(actor: auth()->user(), language: app()->getLocale()));
        $this->metrics = $result->data['metrics'];
    }
}
