<?php
namespace App\Filament\Resources\EmailTemplateResource\Pages;
use App\Filament\Resources\EmailTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
class ViewEmailTemplate extends ViewRecord {
    protected static string $resource = EmailTemplateResource::class;
    protected function getHeaderActions(): array { return [Actions\EditAction::make(), Actions\DeleteAction::make()->action(fn () => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.communications.emailTemplates.delete', parameters: ['template' => $this->record->id]))]; }
}
