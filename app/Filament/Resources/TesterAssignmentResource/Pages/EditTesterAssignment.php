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
            ->hidden(fn () => !in_array($this->record->status, ['pending', 'cancelled'], true)
                || (\App\Contexts\Shared\Infrastructure\AdminReferenceOptions::data('admin.resourceSummaries.metric', ['metric' => 'tester_assignments.has_bug_reports', 'id' => $this->record->id])['count'] ?? false))];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.testing.assignments.update', $data, ['testerAssignment' => $record]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
