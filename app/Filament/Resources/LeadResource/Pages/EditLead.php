<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.enquiries.leads.update',
            new \App\Contexts\Shared\Application\WorkflowInput($data, ['lead' => $record], auth()->user(), app()->getLocale()),
        );
        return $record->fresh();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->action(fn () => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run(
                'admin.enquiries.leads.delete', parameters: ['lead' => $this->record],
            )),
        ];
    }
}
