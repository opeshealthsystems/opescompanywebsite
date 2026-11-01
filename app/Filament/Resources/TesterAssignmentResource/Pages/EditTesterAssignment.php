<?php

namespace App\Filament\Resources\TesterAssignmentResource\Pages;

use App\Filament\Resources\TesterAssignmentResource;
use App\Models\TesterAssignment;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTesterAssignment extends EditRecord
{
    protected static string $resource = TesterAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()
            ->action(fn (TesterAssignment $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.testing.assignments.delete', parameters: ['testerAssignment' => $record->id]))
            ->hidden(fn () => !in_array($this->record->status, ['pending', 'cancelled'], true) || $this->record->bugReports()->exists())];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.testing.assignments.update',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, parameters: ['testerAssignment' => $record], actor: auth()->user(), language: app()->getLocale()),
        );

        return TesterAssignment::query()->findOrFail($record->id);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
