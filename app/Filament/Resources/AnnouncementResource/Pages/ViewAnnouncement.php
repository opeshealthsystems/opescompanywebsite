<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAnnouncement extends ViewRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()->using(function ($record) {
                \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.communications.announcements.delete', parameters: ['announcement' => $record->id]);
                return true;
            }),
        ];
    }
}
