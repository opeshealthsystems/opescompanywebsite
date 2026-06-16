<?php

namespace App\Filament\Pages;

use App\Models\PayrollRun;
use App\Models\User;
use Filament\Pages\Page;

class PayrollHeadcount extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Payroll Headcount';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 8;
    protected static string $view = 'filament.pages.payroll-headcount';

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
        $runs = PayrollRun::where('status', 'completed')
            ->whereYear('period_start', $this->selectedYear)
            ->withCount('entries')
            ->orderBy('period_start')
            ->get();

        $rows = $runs->map(fn ($run) => [
            'reference'  => $run->reference,
            'period'     => $run->period_start->format('M Y'),
            'headcount'  => $run->entries_count,
            'gross'      => (float) $run->total_gross,
            'net'        => (float) $run->total_net,
            'avg_net'    => $run->entries_count > 0 ? (float) $run->total_net / $run->entries_count : 0,
            'currency'   => $run->currency,
        ])->toArray();

        $totalStaff = User::whereHas('roles', fn ($q) =>
            $q->whereIn('name', ['super_admin','admin','support','tester'])
        )->where('is_active', true)->count();

        return [
            'runs'        => $rows,
            'total_staff' => $totalStaff,
            'total_payroll_net' => $runs->sum('total_net'),
        ];
    }

    protected function getViewData(): array
    {
        return ['report' => $this->getReportData()];
    }
}
