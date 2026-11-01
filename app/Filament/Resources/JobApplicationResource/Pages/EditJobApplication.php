<?php

namespace App\Filament\Resources\JobApplicationResource\Pages;

use App\Filament\Resources\JobApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditJobApplication extends EditRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.recruitment.applications.update', $data, ['application' => $record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()->action(fn (Model $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.recruitment.applications.delete', parameters: ['application' => $record->id])),
        ];
    }
}
