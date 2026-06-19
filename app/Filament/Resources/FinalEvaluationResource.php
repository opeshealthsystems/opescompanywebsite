<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinalEvaluationResource\Pages;
use App\Models\CohortMember;
use App\Models\FinalEvaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FinalEvaluationResource extends Resource
{
    protected static ?string $model = FinalEvaluation::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 15;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('cohort_member_id')
                ->label('Cohort member')
                ->options(fn () => CohortMember::with(['user', 'cohort'])->get()
                    ->mapWithKeys(fn (CohortMember $m) => [$m->id => ($m->user?->name ?? 'Member #'.$m->id).' — '.($m->cohort?->name ?? '')]))
                ->searchable()->required()->disabledOn('edit'),
            Forms\Components\Textarea::make('assessment')->rows(4)->required(),
            Forms\Components\Select::make('rating')->options(FinalEvaluation::ratingOptions())->required(),
            Forms\Components\Textarea::make('recommendation')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cohortMember.user.name')->label('Member')->searchable(),
                Tables\Columns\TextColumn::make('cohortMember.cohort.name')->label('Cohort'),
                Tables\Columns\TextColumn::make('rating')->badge()
                    ->formatStateUsing(fn ($state) => FinalEvaluation::ratingOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'outstanding' => 'success', 'strong' => 'info',
                        'satisfactory' => 'gray', 'needs_improvement' => 'warning', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('evaluator.name')->label('Evaluator')->placeholder('—'),
                Tables\Columns\TextColumn::make('evaluated_at')->dateTime(),
            ])
            ->defaultSort('evaluated_at', 'desc')
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFinalEvaluations::route('/'),
            'create' => Pages\CreateFinalEvaluation::route('/create'),
            'view'   => Pages\ViewFinalEvaluation::route('/{record}'),
            'edit'   => Pages\EditFinalEvaluation::route('/{record}/edit'),
        ];
    }
}
