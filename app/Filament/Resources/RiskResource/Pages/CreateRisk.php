<?php
namespace App\Filament\Resources\RiskResource\Pages;
use App\Filament\Resources\RiskResource;
use Filament\Resources\Pages\CreateRecord;
class CreateRisk extends CreateRecord {
    protected static string $resource = RiskResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array {
        $data['reference'] = \App\Models\Risk::generateReference();
        if (!isset($data['risk_score']) || !$data['risk_score']) {
            $data['risk_score'] = \App\Models\Risk::computeScore($data['likelihood'] ?? 'medium', $data['impact'] ?? 'medium');
        }
        return $data;
    }
}
