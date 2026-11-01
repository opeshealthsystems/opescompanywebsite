<?php

namespace App\Filament\Resources\WeeklyReviewResource\Pages;

use App\Filament\Resources\WeeklyReviewResource;
use App\Models\WeeklyReview;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditWeeklyReview extends EditRecord
{
    protected static string $resource = WeeklyReviewResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.validation.weeklyReviews.update',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, parameters: ['weeklyReview' => $record], actor: auth()->user(), language: app()->getLocale()),
        );

        return WeeklyReview::query()->findOrFail($record->id);
    }
}
