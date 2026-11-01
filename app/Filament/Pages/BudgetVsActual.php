<?php

namespace App\Filament\Pages;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use Filament\Pages\Page;

class BudgetVsActual extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $title           = 'Budget vs Actual';
    protected static ?string $navigationLabel = 'Budget vs Actual';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.pages.budget-vs-actual';

    public int $selectedYear;

    public function mount(): void
    {
        $this->selectedYear = now()->year;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getReportData(): array
    {
        return app(WorkflowDispatcher::class)->execute('admin.finance.reports.budgetVsActual',
            new WorkflowInput(values: ['year' => $this->selectedYear], actor: auth()->user(), language: app()->getLocale()))->data;
    }}
