<?php
namespace App\Filament\Resources\PerformanceReviewResource\Pages;
use App\Filament\Resources\PerformanceReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditPerformanceReview extends EditRecord {
    protected static string $resource = PerformanceReviewResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.workforce.performanceReviews.update',
            new \App\Contexts\Shared\Application\WorkflowInput($data, ['review' => $record], auth()->user(), app()->getLocale()),
        );
        return $record->fresh();
    }

    protected function getHeaderActions(): array { return []; }
}
