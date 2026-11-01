<?php
namespace App\Filament\Resources\TrainingRecordResource\Pages;
use App\Filament\Resources\TrainingRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditTrainingRecord extends EditRecord {
    protected static string $resource = TrainingRecordResource::class;
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.workforce.training.update', $data, ['trainingRecord' => $record->id]);
    }
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()->using(function ($record) {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.workforce.training.delete', parameters: ['trainingRecord' => $record->id]);
        return true;
    })]; }
}
