<?php

namespace App\Filament\Pages;

use App\Models\Department;
use App\Models\JobOpening;
use App\Models\LeaveRequest;
use App\Models\User;
use Filament\Pages\Page;

class HrSummary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'HR Summary';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 95;
    protected static string $view = 'filament.pages.hr-summary';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getHeadcountByDepartment(): array
    {
        $departments = Department::where('is_active', true)
            ->withCount('members')
            ->orderByDesc('members_count')
            ->get();

        $unassigned = User::whereNull('department_id')
            ->whereHas('roles', fn($q) => $q->where('name', '!=', 'customer'))
            ->count();

        $result = $departments->map(fn($d) => [
            'name'  => $d->name,
            'count' => $d->members_count,
        ])->toArray();

        if ($unassigned > 0) {
            $result[] = ['name' => 'Unassigned', 'count' => $unassigned];
        }

        return $result;
    }

    public function getLeaveStats(): array
    {
        $thisMonth = LeaveRequest::whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->get();

        return [
            'total_requests' => $thisMonth->count(),
            'approved'       => $thisMonth->where('status', 'approved')->count(),
            'pending'        => $thisMonth->where('status', 'pending')->count(),
            'rejected'       => $thisMonth->where('status', 'rejected')->count(),
        ];
    }

    public function getOpenPositions(): int
    {
        return JobOpening::where('status', 'open')->count();
    }

    public function getTotalHeadcount(): int
    {
        return User::whereHas('roles', fn($q) => $q->where('name', '!=', 'customer'))->count();
    }
}
