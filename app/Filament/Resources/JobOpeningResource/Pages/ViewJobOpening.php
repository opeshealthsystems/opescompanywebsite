<?php
namespace App\Filament\Resources\JobOpeningResource\Pages;
use App\Filament\Resources\JobOpeningResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
class ViewJobOpening extends ViewRecord {
    protected static string $resource = JobOpeningResource::class;
    protected function getHeaderActions(): array { return [Actions\EditAction::make(), Actions\DeleteAction::make()]; }
}
