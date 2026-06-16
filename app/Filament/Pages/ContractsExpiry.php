<?php

namespace App\Filament\Pages;

use App\Models\Contract;
use Carbon\Carbon;
use Filament\Pages\Page;

class ContractsExpiry extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Contracts Expiry';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 98;
    protected static string $view = 'filament.pages.contracts-expiry';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getContractData(): array
    {
        $active = Contract::whereIn('status', ['active', 'pending_renewal'])
            ->whereNotNull('end_date')
            ->orderBy('end_date')
            ->get();

        $today = Carbon::today();

        return $active->map(function (Contract $contract) use ($today) {
            $endDate = Carbon::parse($contract->end_date);
            $daysLeft = $today->diffInDays($endDate, false);

            return [
                'reference'  => $contract->reference,
                'type'       => $contract->type,
                'status'     => $contract->status,
                'value'      => (float) $contract->value,
                'currency'   => $contract->currency ?? 'XAF',
                'end_date'   => $endDate->format('d M Y'),
                'days_left'  => (int) $daysLeft,
                'auto_renew' => (bool) $contract->auto_renew,
            ];
        })->toArray();
    }

    public function getExpiryBuckets(): array
    {
        $data = $this->getContractData();
        return [
            'expired' => ['label' => 'Already Expired',        'rows' => array_values(array_filter($data, fn($r) => $r['days_left'] < 0))],
            'week'    => ['label' => 'Expiring This Week',     'rows' => array_values(array_filter($data, fn($r) => $r['days_left'] >= 0 && $r['days_left'] <= 7))],
            'month'   => ['label' => 'Expiring This Month',    'rows' => array_values(array_filter($data, fn($r) => $r['days_left'] > 7 && $r['days_left'] <= 30))],
            'quarter' => ['label' => 'Expiring in 30–90 Days', 'rows' => array_values(array_filter($data, fn($r) => $r['days_left'] > 30 && $r['days_left'] <= 90))],
            'later'   => ['label' => 'Beyond 90 Days',         'rows' => array_values(array_filter($data, fn($r) => $r['days_left'] > 90))],
        ];
    }
}
