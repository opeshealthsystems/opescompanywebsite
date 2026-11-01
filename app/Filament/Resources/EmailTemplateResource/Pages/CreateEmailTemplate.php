<?php
namespace App\Filament\Resources\EmailTemplateResource\Pages;
use App\Filament\Resources\EmailTemplateResource;
use Filament\Resources\Pages\CreateRecord;
class CreateEmailTemplate extends CreateRecord {
    protected static string $resource = EmailTemplateResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.communications.emailTemplates.create', $data);
    }
}
