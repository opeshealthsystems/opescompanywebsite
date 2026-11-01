<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;

class HrSummary extends Page
{
    protected static ?string $title           = 'HR Summary';
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'HR Summary';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 95;
    protected static string $view = 'filament.pages.hr-summary';

    private ?array $summaryCache = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getHeadcountByDepartment(): array
    {
        return $this->summary()['headcount_by_department'];
    }

    public function getLeaveStats(): array
    {
        return $this->summary()['leave'];
    }

    public function getOpenPositions(): int
    {
        return $this->summary()['open_positions'];
    }

    public function getTotalHeadcount(): int
    {
        return $this->summary()['total_headcount'];
    }

    private function summary(): array
    {
        return $this->summaryCache ??= app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.reports.workforce.summary',
            new \App\Contexts\Shared\Application\WorkflowInput(actor: auth()->user()),
        )->data['summary'];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_headcount_csv')
                ->label('Export Headcount CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function (): \Symfony\Component\HttpFoundation\StreamedResponse {
                    $departments = $this->getHeadcountByDepartment();
                    $leave = $this->getLeaveStats();
                    $rows = ["Department,Headcount\n"];
                    foreach ($departments as $dept) {
                        $rows[] = '"' . $dept['name'] . '",' . $dept['count'] . "\n";
                    }
                    $rows[] = "\n";
                    $rows[] = "Leave Summary (This Month)\n";
                    $rows[] = "Total Requests,Approved,Pending,Rejected\n";
                    $rows[] = implode(',', [
                        $leave['total_requests'],
                        $leave['approved'],
                        $leave['pending'],
                        $leave['rejected'],
                    ]) . "\n";
                    return response()->streamDownload(
                        fn () => print(implode('', $rows)),
                        'hr-summary-' . now()->format('Y-m') . '.csv',
                        ['Content-Type' => 'text/csv']
                    );
                }),
        ];
    }
}
