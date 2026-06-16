<?php
namespace App\Filament\Widgets;

use App\Models\Contract;
use App\Models\Deal;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CrmPipelineWidget extends BaseWidget
{
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 'full';
    protected static bool $isLazy = true;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    protected function getStats(): array
    {
        $openLeads = Lead::whereNotIn('status', ['converted', 'lost', 'disqualified'])->count();

        $pipelineValue = Deal::whereNotIn('stage', ['closed_won', 'closed_lost'])->sum('value');

        $wonThisMonth = Deal::where('stage', 'closed_won')
            ->where('updated_at', '>=', now()->startOfMonth())
            ->sum('value');

        $expiringContracts = Contract::whereIn('status', ['active'])
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', now()->addDays(30))
            ->count();

        return [
            Stat::make('Open Leads', (string) $openLeads)
                ->description('Active in pipeline')
                ->color('info')
                ->icon('heroicon-o-user-plus'),
            Stat::make('Pipeline Value', 'XAF '.number_format((float) $pipelineValue, 0))
                ->description('Open deals total')
                ->color('primary')
                ->icon('heroicon-o-funnel'),
            Stat::make('Won This Month', 'XAF '.number_format((float) $wonThisMonth, 0))
                ->description('Closed-won deals')
                ->color('success')
                ->icon('heroicon-o-trophy'),
            Stat::make('Contracts Expiring', (string) $expiringContracts)
                ->description('Within 30 days')
                ->color($expiringContracts > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-document-check'),
        ];
    }
}
