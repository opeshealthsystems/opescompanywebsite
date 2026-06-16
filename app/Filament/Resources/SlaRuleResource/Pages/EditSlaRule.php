<?php
namespace App\Filament\Resources\SlaRuleResource\Pages;
use App\Filament\Resources\SlaRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditSlaRule extends EditRecord {
    protected static string $resource = SlaRuleResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
