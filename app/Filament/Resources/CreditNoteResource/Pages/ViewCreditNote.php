<?php

namespace App\Filament\Resources\CreditNoteResource\Pages;

use App\Filament\Resources\CreditNoteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCreditNote extends ViewRecord
{
    protected static string $resource = CreditNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()->action(fn () => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.finance.creditNotes.delete', parameters: ['creditNote' => $this->record->id])),
        ];
    }
}
