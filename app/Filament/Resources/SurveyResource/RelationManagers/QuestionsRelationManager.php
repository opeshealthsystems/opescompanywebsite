<?php
namespace App\Filament\Resources\SurveyResource\RelationManagers;

use App\Models\SurveyQuestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('sort_order')
                ->label('Sort Order')
                ->numeric()
                ->default(0),
            Forms\Components\Textarea::make('question')
                ->required()
                ->rows(2)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('question_fr')
                ->label('Question (French)')
                ->rows(2)
                ->nullable()
                ->columnSpanFull(),
            Forms\Components\Select::make('type')
                ->options(SurveyQuestion::typeOptions())
                ->default('text')
                ->required()
                ->live(),
            Forms\Components\TagsInput::make('options')
                ->label('Choices')
                ->placeholder('Add option...')
                ->visible(fn (Forms\Get $get) => $get('type') === 'multiple_choice')
                ->nullable(),
            Forms\Components\Toggle::make('is_required')
                ->label('Required')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('question')->limit(60),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\IconColumn::make('is_required')->label('Req.')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
