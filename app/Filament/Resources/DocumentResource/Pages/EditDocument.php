<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $editable = array_intersect_key($data, array_flip(['title', 'valid_until', 'addressee_user_id', 'addressee_name', 'addressee_email', 'notes']));
        return AdminWorkflowAdapter::record('admin.documents.update', $editable, ['document' => $record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn () => route('documents.pdf', $this->record))
                ->openUrlInNewTab(),
            Actions\Action::make('mark_sent')
                ->label('Mark as Sent')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->hidden(fn () => $this->record->status !== 'draft')
                ->action(fn () => AdminWorkflowAdapter::run('admin.documents.markSent', parameters: ['document' => $this->record]))
                ->after(fn () => $this->refreshFormData(['status'])),
            Actions\Action::make('void')
                ->label('Void Document')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(fn () => in_array($this->record->status, ['signed', 'voided']))
                ->action(fn () => AdminWorkflowAdapter::run('admin.documents.void', parameters: ['document' => $this->record]))
                ->after(fn () => $this->refreshFormData(['status'])),
            Actions\DeleteAction::make()->hidden(fn () => $this->record->status !== 'draft')->using(function ($record) {
                AdminWorkflowAdapter::run('admin.documents.delete', parameters: ['document' => $record->id]);
                return true;
            }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Only drafts can be edited; issued documents use explicit transitions.
        if ($this->record->status !== 'draft') {
            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        }

        return $data;
    }
}
