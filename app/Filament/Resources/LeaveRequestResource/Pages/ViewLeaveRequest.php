<?php

namespace App\Filament\Resources\LeaveRequestResource\Pages;

use App\Filament\Resources\LeaveRequestResource;
use App\Models\LeaveRequest;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLeaveRequest extends ViewRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'pending' && auth()->user()?->hasAnyRole(['super_admin', 'admin']))
                ->action(function () {
                    $this->record->update([
                        'status'      => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);
                    $this->refreshFormData(['status', 'approved_by', 'approved_at']);
                }),
            Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'pending' && auth()->user()?->hasAnyRole(['super_admin', 'admin']))
                ->action(function () {
                    $this->record->update([
                        'status'      => 'rejected',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);
                    $this->refreshFormData(['status', 'approved_by', 'approved_at']);
                }),
        ];
    }
}
