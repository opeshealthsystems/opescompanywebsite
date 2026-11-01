<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

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
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Prevent editing signed or voided documents
        if (in_array($this->record->status, ['signed', 'voided'])) {
            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        }

        return $data;
    }
}
