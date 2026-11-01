<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAsset extends ViewRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make(), Actions\DeleteAction::make()->action(fn () => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.operations.assets.delete', parameters: ['asset' => $this->record->id]))];
    }
}
