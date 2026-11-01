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
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.validation.weeklyReviews.create', $data);
    }
}
