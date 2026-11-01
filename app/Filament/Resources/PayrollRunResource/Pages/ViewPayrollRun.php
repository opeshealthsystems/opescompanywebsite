<?php

namespace App\Filament\Resources\PayrollRunResource\Pages;

use App\Filament\Resources\PayrollRunResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPayrollRun extends ViewRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('mark_complete')
                ->label('Mark Complete & Notify')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn () => $this->record->status === 'processing')
                ->requiresConfirmation()
                ->modalHeading('Complete Payroll Run')
                ->modalDescription('This will mark the run as processed and email each employee their payslip summary. This cannot be undone.')
                ->modalSubmitActionLabel('Complete & Notify')
                ->action(function (): void {
                    \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.finance.payrollRuns.complete', parameters: ['run' => $this->record->id]);
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('generate_entries')
                ->label('Generate Entries')
                ->icon('heroicon-o-bolt')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'draft')
                ->requiresConfirmation()
                ->modalHeading('Generate Payroll Entries')
                ->modalDescription(
                    'This will create one entry per active staff member who does not already have an entry in this run. '
                    . 'Gross salary is taken from the employee\'s profile salary, falling back to their base salary. '
                    . 'Employees already included in this run will be skipped.'
                )
                ->modalSubmitActionLabel('Generate')
                ->action(function () {
                    \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.finance.payrollRuns.generateEntries', parameters: ['run' => $this->record->id]);
                    $this->refreshFormData(['status', 'total_gross', 'total_net']);
                }),
        ];
    }
}
