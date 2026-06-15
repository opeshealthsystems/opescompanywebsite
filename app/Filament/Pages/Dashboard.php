<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'OPES Dashboard';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\OpesDashboardStats::class,
            \App\Filament\Widgets\RecentTicketsWidget::class,
            \App\Filament\Widgets\RecentInvoicesWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}
