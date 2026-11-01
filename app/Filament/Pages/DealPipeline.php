<?php

namespace App\Filament\Pages;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use Filament\Pages\Page;

class DealPipeline extends Page
{
    private ?array $pipelineData = null;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Pipeline Board';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 50;
    protected static string $view = 'filament.pages.deal-pipeline';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getStages(): array
    {
        return $this->pipeline()['stages'];
    }

    public function getDealsByStage(): array
    {
        return $this->pipeline()['deals_by_stage'];
    }

    public function getStageStats(): array
    {
        return $this->pipeline()['stage_stats'];
    }

    public function advanceStage(int $dealId): void
    {
        $stages = array_keys($this->getStages());
        $deal = collect($this->getDealsByStage())->flatten(1)->firstWhere('id', $dealId);
        if (! $deal) return;

        $currentIndex = array_search($deal['stage'], $stages);
        if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
            $nextStage = $stages[$currentIndex + 1];
            AdminWorkflowAdapter::run('admin.crm.deals.update', ['stage' => $nextStage], ['deal' => $dealId]);
            $this->pipelineData = null;

            \Filament\Notifications\Notification::make()
                ->title('Deal moved to ' . ($this->getStages()[$nextStage] ?? $nextStage))
                ->success()
                ->send();
        }
    }

    private function pipeline(): array
    {
        return $this->pipelineData ??= app(WorkflowDispatcher::class)->execute(
            'admin.crm.deals.pipeline',
            new WorkflowInput(actor: auth()->user(), language: app()->getLocale()),
        )->data;
    }
}
