<?php

namespace App\Filament\Resources\CohortResource\RelationManagers;

use App\Models\CohortMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->options(CohortMember::statusOptions())
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Practitioner'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'success',
                        'suspended' => 'warning',
                        'removed'   => 'danger',
                        'completed' => 'info',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('placed_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\Action::make('evaluate')
                    ->label('Evaluate')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->visible(fn (\App\Models\CohortMember $record) => $record->finalEvaluation()->doesntExist())
                    ->form([
                        \Filament\Forms\Components\Textarea::make('assessment')->rows(4)->required(),
                        \Filament\Forms\Components\Select::make('rating')
                            ->options(\App\Models\FinalEvaluation::ratingOptions())->required(),
                        \Filament\Forms\Components\Textarea::make('recommendation')->rows(3),
                    ])
                    ->action(function (\App\Models\CohortMember $record, array $data) {
                        \App\Models\FinalEvaluation::create(array_merge(
                            [
                                'cohort_member_id' => $record->id,
                                'assessment'       => $data['assessment'],
                                'rating'           => $data['rating'],
                                'recommendation'   => $data['recommendation'] ?? null,
                            ],
                            \App\Models\FinalEvaluation::snapshotData($record, auth()->id())
                        ));
                        \Filament\Notifications\Notification::make()->title('Final evaluation recorded.')->success()->send();
                    }),
            ]);
    }
}
