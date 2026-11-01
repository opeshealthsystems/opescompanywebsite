<?php

namespace App\Filament\Resources\CreditNoteResource\Pages;

use App\Filament\Resources\CreditNoteResource;
use App\Models\CreditNote;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCreditNote extends CreateRecord
{
    protected static string $resource = CreditNoteResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.finance.creditNotes.create', $data);
    }
}
