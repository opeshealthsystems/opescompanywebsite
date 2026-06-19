<?php

namespace App\Filament\Pages;

use App\Support\ValidationMetrics;
use Filament\Pages\Page;

class ValidationPractitionerDashboard extends Page
{
    protected static ?string $title           = 'Practitioner Performance';
    protected static ?string $navigationIcon  = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Practitioner Performance';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 13;
    protected static string  $view            = 'filament.pages.validation-practitioner-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getLeaderboard(): array
    {
        return app(ValidationMetrics::class)->practitionerLeaderboard();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('export_csv')
                ->label('Export CSV')->icon('heroicon-o-arrow-down-tray')->color('gray')
                ->action(function (): \Symfony\Component\HttpFoundation\StreamedResponse {
                    $rows = $this->getLeaderboard();
                    return response()->streamDownload(function () use ($rows) {
                        echo "Practitioner,Cohort,Sessions,Issues Found,Accepted,Retests\n";
                        foreach ($rows as $r) {
                            echo '"'.$r['member'].'","'.$r['cohort'].'",'.$r['sessions'].','.$r['issues_found'].','.$r['issues_accepted'].','.$r['retests']."\n";
                        }
                    }, 'practitioner-performance-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
                }),
        ];
    }
}
