<?php

namespace App\Filament\Pages;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use Filament\Pages\Page;

class CashFlow extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationLabel = 'Cash Flow';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 7;
    protected static string $view = 'filament.pages.cash-flow';

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
        return app(WorkflowDispatcher::class)->execute('admin.finance.reports.cashFlow',
            new WorkflowInput(values: ['year' => $this->selectedYear], actor: auth()->user(), language: app()->getLocale()))->data;
    }
    protected function getViewData(): array
    {
        return ['report' => $this->getReportData()];
    }
}
