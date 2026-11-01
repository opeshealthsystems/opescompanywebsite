<?php
namespace App\Filament\Resources\PerformanceReviewResource\Pages;
use App\Filament\Resources\PerformanceReviewResource;
use Filament\Resources\Pages\CreateRecord;
class CreatePerformanceReview extends CreateRecord {
    protected static string $resource = PerformanceReviewResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.workforce.performanceReviews.create', $data);
    }
}
