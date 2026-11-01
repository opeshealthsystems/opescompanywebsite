<?php
namespace App\Filament\Resources\RiskResource\Pages;

use App\Filament\Resources\RiskResource;
use App\Models\Risk;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRisk extends CreateRecord
{
    protected static string $resource = RiskResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.workforce.risks.create', $data);
    }
}
