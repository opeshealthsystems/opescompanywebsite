<?php

namespace App\Filament\Resources\DemoRequestResource\Pages;

use App\Filament\Resources\DemoRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDemoRequest extends EditRecord
{
    protected static string $resource = DemoRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.crm.demoRequests.update', $data, ['demoRequest' => $record->id]);
    }

    protected function handleRecordDeletion(\Illuminate\Database\Eloquent\Model $record): void
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.crm.demoRequests.delete', parameters: ['demoRequest' => $record->id]);
    }
}
