<?php
namespace App\Filament\Resources\SlaRuleResource\Pages;
use App\Filament\Resources\SlaRuleResource;
use Filament\Resources\Pages\CreateRecord;
class CreateSlaRule extends CreateRecord {
    protected static string $resource = SlaRuleResource::class;
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.support.slaRules.create', $data);
    }
}
