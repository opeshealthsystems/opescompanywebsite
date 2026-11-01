<?php

namespace App\Filament\Resources\DocumentTemplateResource\Pages;

use App\Filament\Resources\DocumentTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentTemplate extends EditRecord
{
    protected static string $resource = DocumentTemplateResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.documents.templates.update', $data, ['template' => $record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn () => $this->record->loadCount('documents')->documents_count > 0)
                ->using(function ($record) {
                    \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.documents.templates.delete', parameters: ['template' => $record->id]);
                    return true;
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
