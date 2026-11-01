<?php

namespace App\Filament\Resources\CreditNoteResource\Pages;

use App\Filament\Resources\CreditNoteResource;
use App\Models\CreditNote;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCreditNote extends EditRecord
{
    protected static string $resource = CreditNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()->action(fn (CreditNote $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.finance.creditNotes.delete', parameters: ['creditNote' => $record->id]))];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['items'] = \App\Contexts\Shared\Infrastructure\AdminReferenceOptions::routeData('admin.finance.creditNoteItems', ['creditNote' => $this->record->id])['items'] ?? [];
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.finance.creditNotes.update', $data, ['creditNote' => $record]);
    }
}
