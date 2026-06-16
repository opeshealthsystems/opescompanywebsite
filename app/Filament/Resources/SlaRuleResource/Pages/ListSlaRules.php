<?php
namespace App\Filament\Resources\SlaRuleResource\Pages;
use App\Filament\Resources\SlaRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListSlaRules extends ListRecords {
    protected static string $resource = SlaRuleResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
