<?php

namespace App\Filament\Pages;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use Filament\Actions\Action;
use Filament\Pages\Page;

class ArAging extends Page
{
    private ?array $reportCache = null;
    protected static ?string $title           = 'A/R Aging';
    protected static ?string $navigationIcon  = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'A/R Aging';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 96;
    protected static string $view = 'filament.pages.ar-aging';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getAgingData(): array
    {
        return $this->report()['buckets'];
    }

    public function getTotalOutstanding(): float
    {
        return $this->report()['total_outstanding'];
    }

    private function report(): array
    {
        return $this->reportCache ??= app(WorkflowDispatcher::class)->execute('admin.finance.reports.accountsReceivableAging',
            new WorkflowInput(actor: auth()->user(), language: app()->getLocale()))->data;
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function (): \Symfony\Component\HttpFoundation\StreamedResponse {
                    $buckets = $this->getAgingData();
                    $rows = ["Bucket,Reference,Status,Due Date,Days Overdue,Amount,Currency\n"];
                    foreach ($buckets as $bucket) {
                        foreach ($bucket['invoices'] as $inv) {
                            $rows[] = implode(',', [
                                '"' . $bucket['label'] . '"',
                                $inv['reference'],
                                $inv['status'],
                                $inv['due_date'],
                                $inv['days_overdue'] ?? 0,
                                number_format($inv['amount'], 2),
                                $inv['currency'],
                            ]) . "\n";
                        }
                    }
                    return response()->streamDownload(
                        fn () => print(implode('', $rows)),
                        'ar-aging-' . now()->format('Y-m-d') . '.csv',
                        ['Content-Type' => 'text/csv']
                    );
                }),
        ];
    }
}
