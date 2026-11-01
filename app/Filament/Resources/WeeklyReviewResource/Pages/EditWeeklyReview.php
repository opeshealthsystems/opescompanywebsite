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
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.validation.weeklyReviews.update', $data, ['weeklyReview' => $record]);
    }
}
