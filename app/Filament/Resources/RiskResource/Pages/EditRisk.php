<?php
namespace App\Filament\Resources\RiskResource\Pages;
use App\Filament\Resources\RiskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditRisk extends EditRecord {
    protected static string $resource = RiskResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
    protected function mutateFormDataBeforeSave(array $data): array {
        $data['risk_score'] = \App\Models\Risk::computeScore($data['likelihood'] ?? 'medium', $data['impact'] ?? 'medium');
        return $data;
    }
}
