<?php
namespace App\Filament\Resources\SlaRuleResource\Pages;
use App\Filament\Resources\SlaRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditSlaRule extends EditRecord {
    protected static string $resource = SlaRuleResource::class;
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.support.slaRules.update', $data, ['slaRule' => $record->id]);
    }
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()->using(function ($record) {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.support.slaRules.delete', parameters: ['slaRule' => $record->id]);
        return true;
    })]; }
}
