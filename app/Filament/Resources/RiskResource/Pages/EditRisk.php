<?php
namespace App\Filament\Resources\RiskResource\Pages;

use App\Filament\Resources\RiskResource;
use App\Models\Risk;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRisk extends EditRecord
{
    protected static string $resource = RiskResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.workforce.risks.update', $data, ['risk' => $record]);
    }
}
