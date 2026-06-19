<?php

namespace App\Filament\Resources\PractitionerApplicationResource\Actions;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\PractitionerApplication;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class PlaceInCohortAction
{
    public static function make(?string $name = null): Action
    {
        return Action::make('place_in_cohort')
            ->label('Place in Cohort')
            ->icon('heroicon-o-user-plus')
            ->color('primary')
            ->visible(fn (PractitionerApplication $record): bool =>
                $record->status === 'approved'
                && optional($record->program)->program_type === 'validation'
                && ! $record->practitioner->cohortMembers()
                    ->whereHas('cohort', fn ($q) => $q->where('practitioner_program_id', $record->program_id))
                    ->exists())
            ->form([
                Forms\Components\Select::make('cohort_id')
                    ->label('Cohort')
                    ->required()
                    ->options(fn (PractitionerApplication $record) =>
                        Cohort::where('practitioner_program_id', $record->program_id)
                            ->where('status', 'active')
                            ->pluck('name', 'id')),
            ])
            ->action(function (PractitionerApplication $record, array $data): void {
                CohortMember::create([
                    'cohort_id' => $data['cohort_id'],
                    'user_id'   => $record->practitioner_id,
                    'status'    => 'active',
                    'placed_at' => now(),
                ]);
                Notification::make()->title('Practitioner placed in cohort.')->success()->send();
            });
    }
}
