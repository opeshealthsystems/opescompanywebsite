<?php
namespace App\Filament\Widgets;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PeopleOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static bool $isLazy = true;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    protected function getStats(): array
    {
        $metrics = app(WorkflowDispatcher::class)->execute('admin.reports.peopleSummary',
            new WorkflowInput(actor: auth()->user(), language: app()->getLocale()))->data;

        return [
            Stat::make('Total Headcount', (string) $metrics['headcount'])
                ->description('Active staff members')
                ->color('info')
                ->icon('heroicon-o-users'),
            Stat::make('Pending Leave Requests', (string) $metrics['pending_leave'])
                ->description('Awaiting approval')
                ->color($metrics['pending_leave'] > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-calendar-days'),
            Stat::make('Absent Today', (string) $metrics['absent_today'])
                ->description('Marked absent')
                ->color($metrics['absent_today'] > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-user-minus'),
            Stat::make('Open Positions', (string) $metrics['open_positions'])
                ->description('Active job openings')
                ->color($metrics['open_positions'] > 0 ? 'info' : 'gray')
                ->icon('heroicon-o-briefcase'),
        ];
    }
}
