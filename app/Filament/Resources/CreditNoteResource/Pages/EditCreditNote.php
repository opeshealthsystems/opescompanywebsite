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
        $data['items'] = $this->record->items()->get(['description', 'quantity', 'unit_price'])->toArray();
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.finance.creditNotes.update',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, parameters: ['creditNote' => $record], actor: auth()->user(), language: app()->getLocale()),
        );

        return CreditNote::query()->findOrFail($record->id);
    }
}
