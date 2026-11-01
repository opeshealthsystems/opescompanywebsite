<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.catalogue.products.update', $data, ['product' => $record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\Action::make('archive')
                ->label('Archive product')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.catalogue.products.archive', parameters: ['product' => $this->record->id]);
                    $this->redirect(static::getResource()::getUrl('index'));
                }),
        ];
    }
}
