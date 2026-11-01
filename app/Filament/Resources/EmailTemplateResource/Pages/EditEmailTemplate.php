<?php
namespace App\Filament\Resources\EmailTemplateResource\Pages;
use App\Filament\Resources\EmailTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
class EditEmailTemplate extends EditRecord {
    protected static string $resource = EmailTemplateResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.communications.emailTemplates.update', $data, ['template' => $record->id]);
    }

    protected function handleRecordDeletion(Model $record): void
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.communications.emailTemplates.delete', parameters: ['template' => $record->id]);
    }
}
