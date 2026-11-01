<?php
namespace App\Filament\Resources\RiskResource\Pages;

use App\Filament\Resources\RiskResource;
use App\Models\Risk;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRisk extends EditRecord
{
    protected static string $resource = RiskResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.workforce.risks.update',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, parameters: ['risk' => $record], actor: auth()->user(), language: app()->getLocale()),
        );

        return Risk::query()->findOrFail($record->id);
    }
}
