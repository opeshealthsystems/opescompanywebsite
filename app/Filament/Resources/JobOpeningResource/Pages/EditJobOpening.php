<?php
namespace App\Filament\Resources\JobOpeningResource\Pages;
use App\Filament\Resources\JobOpeningResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
class EditJobOpening extends EditRecord {
    protected static string $resource = JobOpeningResource::class;
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.recruitment.openings.update', $data, ['opening' => $record->id]);
    }
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()->action(fn (Model $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.recruitment.openings.delete', parameters: ['opening' => $record->id]))]; }
}
