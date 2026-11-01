<?php
namespace App\Filament\Resources\TrainingRecordResource\Pages;
use App\Filament\Resources\TrainingRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
class ViewTrainingRecord extends ViewRecord {
    protected static string $resource = TrainingRecordResource::class;
    protected function getHeaderActions(): array { return [Actions\EditAction::make(), Actions\DeleteAction::make()->using(function ($record) {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.workforce.training.delete', parameters: ['trainingRecord' => $record->id]);
        return true;
    })]; }
}
