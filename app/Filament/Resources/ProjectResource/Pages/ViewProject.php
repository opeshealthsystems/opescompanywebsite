<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make(), Actions\DeleteAction::make()->action(fn () => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('administration.projects.delete', parameters: ['project' => $this->record->id]))];
    }
}
