<?php

namespace App\Filament\Pages;

use App\Models\Deal;
use Filament\Pages\Page;

class DealPipeline extends Page
{
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
        return Deal::stageOptions();
    }

    public function getDealsByStage(): array
    {
        $deals = Deal::with(['owner', 'lead'])
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->orderByDesc('value')
            ->get()
            ->groupBy('stage');

        return $deals->toArray();
    }

    public function getStageStats(): array
    {
        return Deal::selectRaw('stage, COUNT(*) as count, SUM(value) as total_value, AVG(probability) as avg_probability')
            ->groupBy('stage')
            ->get()
            ->keyBy('stage')
            ->toArray();
    }

    public function advanceStage(int $dealId): void
    {
        $stages = array_keys($this->getStages());
        $deal = Deal::find($dealId);
        if (! $deal) {
            return;
        }

        $currentIndex = array_search($deal->stage, $stages);
        if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
            $nextStage = $stages[$currentIndex + 1];
            $deal->update(['stage' => $nextStage]);

            \Filament\Notifications\Notification::make()
                ->title('Deal moved to ' . (Deal::stageOptions()[$nextStage] ?? $nextStage))
                ->success()
                ->send();
        }
    }
}
