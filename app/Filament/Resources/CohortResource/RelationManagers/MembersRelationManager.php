<?php

namespace App\Filament\Resources\CohortResource\RelationManagers;

use App\Models\CohortMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected function getTableQuery(): Builder
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::query(
            \App\Contexts\Validation\Application\Queries\AdminCohortQueries::class,
            'memberQuery',
            [$this->getOwnerRecord()],
        );
    }

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
                Tables\Actions\EditAction::make()->action(function (CohortMember $record, array $data): void {
                    \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.validation.cohortMembers.update', $data,
                        ['cohort' => $this->getOwnerRecord()->id, 'member' => $record->id]);
                }),
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
                        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
                            'admin.validation.finalEvaluations.create',
                            new \App\Contexts\Shared\Application\WorkflowInput($data + ['cohort_member_id' => $record->id], [], auth()->user(), app()->getLocale()),
                        );
                        \Filament\Notifications\Notification::make()->title('Final evaluation recorded.')->success()->send();
                    }),
            ]);
    }
}
