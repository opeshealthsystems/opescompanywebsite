<?php
namespace App\Filament\Widgets;

use App\Models\AttendanceRecord;
use App\Models\JobOpening;
use App\Models\LeaveRequest;
use App\Models\User;
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
        $headcount = User::whereHas('roles', fn ($q) => $q->where('name', '!=', 'customer'))->count();

        $pendingLeave = LeaveRequest::where('status', 'pending')->count();

        $absentToday = AttendanceRecord::whereDate('date', today())->where('status', 'absent')->count();

        $openPositions = JobOpening::where('status', 'open')->count();

        return [
            Stat::make('Total Headcount', (string) $headcount)
                ->description('Active staff members')
                ->color('info')
                ->icon('heroicon-o-users'),
            Stat::make('Pending Leave Requests', (string) $pendingLeave)
                ->description('Awaiting approval')
                ->color($pendingLeave > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-calendar-days'),
            Stat::make('Absent Today', (string) $absentToday)
                ->description('Marked absent')
                ->color($absentToday > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-user-minus'),
            Stat::make('Open Positions', (string) $openPositions)
                ->description('Active job openings')
                ->color($openPositions > 0 ? 'info' : 'gray')
                ->icon('heroicon-o-briefcase'),
        ];
    }
}
