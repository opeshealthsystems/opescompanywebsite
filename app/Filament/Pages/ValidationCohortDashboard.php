<?php

namespace App\Filament\Pages;

use App\Support\ValidationMetrics;
use Filament\Pages\Page;

class ValidationCohortDashboard extends Page
{
    protected static ?string $title           = 'Cohort Progress';
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Cohort Progress';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 10;
    protected static string  $view            = 'filament.pages.validation-cohort-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getRows(): array
    {
        return app(ValidationMetrics::class)->cohortProgress();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('export_csv')
                ->label('Export CSV')->icon('heroicon-o-arrow-down-tray')->color('gray')
                ->action(function (): \Symfony\Component\HttpFoundation\StreamedResponse {
                    $rows = $this->getRows();
                    return response()->streamDownload(function () use ($rows) {
                        echo "Cohort,Status,Active Members,Sessions,Covered,Assigned,Coverage %,Issues\n";
                        foreach ($rows as $r) {
                            echo '"'.$r['name'].'",'.$r['status'].','.$r['active_members'].','.$r['sessions'].','.$r['covered_test_cases'].','.$r['assigned_test_cases'].','.$r['coverage_pct'].','.$r['issues']."\n";
                        }
                    }, 'cohort-progress-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
                }),
        ];
    }
}
