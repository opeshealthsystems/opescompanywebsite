<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class ArAging extends Page
{
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
        $invoices = Invoice::whereNotIn('status', ['paid', 'cancelled', 'draft'])
            ->with('items')
            ->get();

        $buckets = [
            'current'  => ['label' => 'Current (not yet due)', 'invoices' => [], 'total' => 0],
            '1_30'     => ['label' => '1–30 Days Overdue',      'invoices' => [], 'total' => 0],
            '31_60'    => ['label' => '31–60 Days Overdue',     'invoices' => [], 'total' => 0],
            '61_90'    => ['label' => '61–90 Days Overdue',     'invoices' => [], 'total' => 0],
            'over_90'  => ['label' => 'Over 90 Days Overdue',   'invoices' => [], 'total' => 0],
        ];

        $today = Carbon::today();

        foreach ($invoices as $invoice) {
            $due = $invoice->due_date ? Carbon::parse($invoice->due_date) : null;
            $daysOverdue = $due ? $today->diffInDays($due, false) * -1 : null;

            $item = [
                'reference'    => $invoice->invoice_number,
                'amount'       => (float) $invoice->grand_total,
                'currency'     => $invoice->currency ?? 'XAF',
                'status'       => $invoice->status,
                'due_date'     => $due?->format('d M Y') ?? '—',
                'days_overdue' => $daysOverdue !== null ? (int) round($daysOverdue) : null,
            ];

            if ($daysOverdue === null || $daysOverdue <= 0) {
                $key = 'current';
            } elseif ($daysOverdue <= 30) {
                $key = '1_30';
            } elseif ($daysOverdue <= 60) {
                $key = '31_60';
            } elseif ($daysOverdue <= 90) {
                $key = '61_90';
            } else {
                $key = 'over_90';
            }

            $buckets[$key]['invoices'][] = $item;
            $buckets[$key]['total'] += $item['amount'];
        }

        return $buckets;
    }

    public function getTotalOutstanding(): float
    {
        return (float) DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->whereNotIn('invoices.status', ['paid', 'cancelled', 'draft'])
            ->sum('invoice_items.total');
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
