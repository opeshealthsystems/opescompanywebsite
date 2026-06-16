<?php

namespace App\Filament\Resources\PayrollRunResource\Pages;

use App\Filament\Resources\PayrollRunResource;
use App\Models\PayrollEntry;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPayrollRun extends ViewRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

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
                    $run = $this->record;

                    // Collect user IDs that already have an entry in this run
                    $existingUserIds = $run->entries()->pluck('user_id')->toArray();

                    // Active staff = users with any non-customer role, with their EmployeeProfile eager-loaded
                    $employees = User::whereHas('roles', fn ($q) =>
                            $q->whereIn('name', ['super_admin', 'admin', 'support', 'tester'])
                        )
                        ->with('employeeProfile')
                        ->whereNotIn('id', $existingUserIds)
                        ->get();

                    $defaultDeductionTypes = \App\Models\PayrollDeductionType::defaultDeductions();

                    $created = 0;
                    foreach ($employees as $employee) {
                        $profile = $employee->employeeProfile;

                        // Prefer EmployeeProfile salary; fall back to users.base_salary; default 0
                        $gross    = (float) ($profile?->salary ?? $employee->base_salary ?? 0);
                        $currency = $profile?->currency ?? $run->currency;

                        // Auto-populate default deductions from configured deduction types
                        $deductions = $defaultDeductionTypes->map(fn ($type) => [
                            'label'  => $type->name,
                            'amount' => $type->calculateAmount($gross),
                        ])->toArray();

                        $totalDeductions = collect($deductions)->sum('amount');
                        $net = max(0, $gross - $totalDeductions);

                        $run->entries()->create([
                            'user_id'          => $employee->id,
                            'gross_salary'     => $gross,
                            'deductions'       => $deductions,
                            'total_deductions' => $totalDeductions,
                            'net_salary'       => $net,
                            'currency'         => $currency,
                            'status'           => 'pending',
                        ]);

                        $created++;
                    }

                    if ($created > 0) {
                        // Advance status to processing only if still in draft
                        if ($run->status === 'draft') {
                            $run->update(['status' => 'processing']);
                        }
                        $run->recalculateTotals();
                    }

                    $this->refreshFormData(['status', 'total_gross', 'total_net']);

                    Notification::make()
                        ->title($created > 0
                            ? "Generated {$created} payroll " . str('entry')->plural($created)
                            : 'No new entries — all active staff are already included')
                        ->status($created > 0 ? 'success' : 'warning')
                        ->send();
                }),
        ];
    }
}
