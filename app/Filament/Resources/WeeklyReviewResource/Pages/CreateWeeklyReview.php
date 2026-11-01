<?php

namespace App\Filament\Resources\WeeklyReviewResource\Pages;

use App\Filament\Resources\WeeklyReviewResource;
use App\Models\WeeklyReview;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateWeeklyReview extends CreateRecord
{
    protected static string $resource = WeeklyReviewResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $result = app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.validation.weeklyReviews.create',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, actor: auth()->user(), language: app()->getLocale()),
        );

        return WeeklyReview::query()->findOrFail($result->data['id']);
    }
}
