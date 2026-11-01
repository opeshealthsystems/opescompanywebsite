<?php

namespace App\Filament\Resources\LicenseResource\Pages;

use App\Filament\Resources\LicenseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLicense extends EditRecord
{
    protected static string $resource = LicenseResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('administration.licenses.update', $data, ['license' => $record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()->using(function ($record) { \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('administration.licenses.delete', parameters: ['license' => $record->id]); return true; })];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
