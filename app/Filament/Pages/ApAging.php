<?php

namespace App\Filament\Pages;

use App\Models\SupplierBill;
use Carbon\Carbon;
use Filament\Pages\Page;

class ApAging extends Page
{
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
        $bills = SupplierBill::whereNotIn('status', ['paid', 'draft'])
            ->get();

        $buckets = [
            'current'  => ['label' => 'Current (not yet due)', 'bills' => [], 'total' => 0],
            '1_30'     => ['label' => '1–30 Days Overdue',     'bills' => [], 'total' => 0],
            '31_60'    => ['label' => '31–60 Days Overdue',    'bills' => [], 'total' => 0],
            '61_90'    => ['label' => '61–90 Days Overdue',    'bills' => [], 'total' => 0],
            'over_90'  => ['label' => 'Over 90 Days Overdue',  'bills' => [], 'total' => 0],
        ];

        $today = Carbon::today();

        foreach ($bills as $bill) {
            $due = $bill->due_date ? Carbon::parse($bill->due_date) : null;
            $daysOverdue = $due ? $today->diffInDays($due, false) * -1 : null;

            $item = [
                'reference'    => $bill->reference,
                'vendor'       => $bill->vendor?->name ?? $bill->vendor_name ?? '—',
                'bill_number'  => $bill->bill_number ?? '—',
                'amount'       => (float) $bill->total,
                'currency'     => $bill->currency ?? 'XAF',
                'status'       => $bill->status,
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

            $buckets[$key]['bills'][] = $item;
            $buckets[$key]['total'] += $item['amount'];
        }

        return $buckets;
    }

    public function getTotalOutstanding(): float
    {
        return SupplierBill::whereNotIn('status', ['paid', 'draft'])->sum('total');
    }
}
