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
                ->label('Generate Entries from Staff')
                ->icon('heroicon-o-user-group')
                ->color('info')
                ->visible(fn () => $this->record->status === 'draft')
                ->requiresConfirmation()
                ->action(function () {
                    $run = $this->record;

                    $users = User::whereHas('employeeProfile', fn ($q) => $q->where('salary', '>', 0))
                        ->with('employeeProfile')
                        ->get();

                    $count = 0;
                    foreach ($users as $user) {
                        $profile = $user->employeeProfile;

                        // Remove any existing entry for this user in this run
                        PayrollEntry::where('payroll_run_id', $run->id)
                            ->where('user_id', $user->id)
                            ->delete();

                        PayrollEntry::create([
                            'payroll_run_id'   => $run->id,
                            'user_id'          => $user->id,
                            'gross_salary'     => $profile->salary,
                            'deductions'       => [],
                            'total_deductions' => 0,
                            'net_salary'       => $profile->salary,
                            'currency'         => $profile->currency ?? $run->currency,
                            'status'           => 'pending',
                        ]);

                        $count++;
                    }

                    $run->update(['status' => 'processing']);
                    $run->recalculateTotals();

                    $this->refreshFormData(['status', 'total_gross', 'total_net']);

                    Notification::make()
                        ->title("Generated {$count} entries")
                        ->success()
                        ->send();
                }),
        ];
    }
}
