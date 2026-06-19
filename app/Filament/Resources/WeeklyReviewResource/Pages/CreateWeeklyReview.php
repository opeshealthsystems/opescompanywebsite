<?php

namespace App\Filament\Resources\WeeklyReviewResource\Pages;

use App\Filament\Resources\WeeklyReviewResource;
use App\Models\Cohort;
use App\Models\WeeklyReview;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateWeeklyReview extends CreateRecord
{
    protected static string $resource = WeeklyReviewResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $cohort = Cohort::findOrFail($data['cohort_id']);
        return array_merge(
            $data,
            WeeklyReview::snapshotData($cohort, Carbon::parse($data['week_start']), auth()->id())
        );
    }
}
