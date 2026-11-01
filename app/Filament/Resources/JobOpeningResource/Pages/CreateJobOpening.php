<?php
namespace App\Filament\Resources\JobOpeningResource\Pages;
use App\Filament\Resources\JobOpeningResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
class CreateJobOpening extends CreateRecord {
    protected static string $resource = JobOpeningResource::class;
    protected function handleRecordCreation(array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.recruitment.openings.create', $data);
    }
}
