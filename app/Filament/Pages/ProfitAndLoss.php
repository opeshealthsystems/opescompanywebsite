<?php

namespace App\Filament\Pages;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use Filament\Actions\Action;
use Filament\Pages\Page;

class ProfitAndLoss extends Page
{
    protected static ?string $title           = 'P&L Statement';
    protected static ?string $navigationIcon  = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'P&L Statement';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 6;
    protected static string $view = 'filament.pages.profit-and-loss';

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
        return app(WorkflowDispatcher::class)->execute('admin.finance.reports.profitAndLoss',
            new WorkflowInput(values: ['year' => $this->selectedYear], actor: auth()->user(), language: app()->getLocale()))->data;
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function (): \Symfony\Component\HttpFoundation\StreamedResponse {
                    $data = $this->getReportData();
                    $rows = ["Month,Revenue,Expenses,Payroll,Total Costs,Net\n"];
                    foreach ($data['months'] as $m) {
                        $rows[] = implode(',', [
                            $m['month'],
                            number_format($m['revenue'], 2),
                            number_format($m['expenses'], 2),
                            number_format($m['payroll'], 2),
                            number_format($m['total_costs'], 2),
                            number_format($m['net'], 2),
                        ]) . "\n";
                    }
                    $rows[] = implode(',', [
                        'TOTAL',
                        number_format($data['total_revenue'], 2),
                        number_format($data['total_expenses'], 2),
                        number_format($data['total_payroll'], 2),
                        number_format($data['total_costs'], 2),
                        number_format($data['net'], 2),
                    ]) . "\n";
                    return response()->streamDownload(
                        fn () => print(implode('', $rows)),
                        'pl-statement-' . $this->selectedYear . '.csv',
                        ['Content-Type' => 'text/csv']
                    );
                }),
        ];
    }

    protected function getViewData(): array
    {
        return ['report' => $this->getReportData()];
    }
}
