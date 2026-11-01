<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use App\Models\Asset;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()
            ->action(fn (Asset $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.operations.assets.delete', parameters: ['asset' => $record->id]))
            ->hidden(fn () => !in_array($this->record->status, ['retired', 'disposed'], true))];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.operations.assets.update',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, parameters: ['asset' => $record], actor: auth()->user(), language: app()->getLocale()),
        );

        return Asset::query()->findOrFail($record->id);
    }
}
