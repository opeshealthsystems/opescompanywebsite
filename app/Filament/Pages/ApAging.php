<?php

namespace App\Filament\Pages;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use Filament\Actions\Action;
use Filament\Pages\Page;

class ApAging extends Page
{
    private ?array $reportCache = null;
    protected static ?string $title           = 'A/P Aging';
    protected static ?string $navigationIcon  = 'heroicon-o-inbox-stack';
    protected static ?string $navigationLabel = 'A/P Aging';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 97;
    protected static string $view = 'filament.pages.ap-aging';

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
        return $this->reportCache ??= app(WorkflowDispatcher::class)->execute('admin.finance.reports.accountsPayableAging',
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
                    $rows = ["Bucket,Reference,Vendor,Bill No,Status,Due Date,Days Overdue,Amount,Currency\n"];
                    foreach ($buckets as $bucket) {
                        foreach ($bucket['bills'] as $bill) {
                            $rows[] = implode(',', [
                                '"' . $bucket['label'] . '"',
                                $bill['reference'],
                                '"' . $bill['vendor'] . '"',
                                $bill['bill_number'],
                                $bill['status'],
                                $bill['due_date'],
                                $bill['days_overdue'] ?? 0,
                                number_format($bill['amount'], 2),
                                $bill['currency'],
                            ]) . "\n";
                        }
                    }
                    return response()->streamDownload(
                        fn () => print(implode('', $rows)),
                        'ap-aging-' . now()->format('Y-m-d') . '.csv',
                        ['Content-Type' => 'text/csv']
                    );
                }),
        ];
    }
}
