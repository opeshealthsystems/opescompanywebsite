<?php
namespace App\Filament\Widgets;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
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
        $metrics = app(WorkflowDispatcher::class)->execute('admin.reports.crmSummary',
            new WorkflowInput(actor: auth()->user(), language: app()->getLocale()))->data;

        return [
            Stat::make('Open Leads', (string) $metrics['open_leads'])
                ->description('Active in pipeline')
                ->color('info')
                ->icon('heroicon-o-user-plus'),
            Stat::make('Pipeline Value', 'XAF '.number_format((float) $metrics['pipeline_value'], 0))
                ->description('Open deals total')
                ->color('primary')
                ->icon('heroicon-o-funnel'),
            Stat::make('Won This Month', 'XAF '.number_format((float) $metrics['won_this_month'], 0))
                ->description('Closed-won deals')
                ->color('success')
                ->icon('heroicon-o-trophy'),
            Stat::make('Contracts Expiring', (string) $metrics['contracts_expiring'])
                ->description('Within 30 days')
                ->color($metrics['contracts_expiring'] > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-document-check'),
        ];
    }
}
