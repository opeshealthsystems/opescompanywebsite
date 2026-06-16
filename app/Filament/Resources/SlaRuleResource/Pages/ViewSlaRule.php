<?php
namespace App\Filament\Resources\SlaRuleResource\Pages;
use App\Filament\Resources\SlaRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
class ViewSlaRule extends ViewRecord {
    protected static string $resource = SlaRuleResource::class;
    protected function getHeaderActions(): array {
        return [Actions\EditAction::make(), Actions\DeleteAction::make()];
    }
}
