<?php
namespace App\Filament\Resources\TrainingRecordResource\Pages;
use App\Filament\Resources\TrainingRecordResource;
use Filament\Resources\Pages\CreateRecord;
class CreateTrainingRecord extends CreateRecord {
    protected static string $resource = TrainingRecordResource::class;
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.workforce.training.create', $data);
    }
}
