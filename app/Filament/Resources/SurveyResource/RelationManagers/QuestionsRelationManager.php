<?php
namespace App\Filament\Resources\SurveyResource\RelationManagers;

use App\Models\SurveyQuestion;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected function getTableQuery(): Builder
    {
        return AdminWorkflowAdapter::query(
            \App\Contexts\Programmes\Application\Queries\AdminSurveyQueries::class,
            'questionQuery',
            [$this->getOwnerRecord()],
        );
    }

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
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('question')->limit(60),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\IconColumn::make('is_required')->label('Req.')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn () => $this->getOwnerRecord()->status === 'draft')
                    ->using(fn (SurveyQuestion $record, array $data) => AdminWorkflowAdapter::record('admin.programmes.surveyQuestions.update', $data, ['survey' => $this->getOwnerRecord()->id, 'question' => $record->id])),
                Tables\Actions\DeleteAction::make()->visible(fn () => $this->getOwnerRecord()->status === 'draft')
                    ->using(function (SurveyQuestion $record) { AdminWorkflowAdapter::run('admin.programmes.surveyQuestions.delete', parameters: ['survey' => $this->getOwnerRecord()->id, 'question' => $record->id]); return true; }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->visible(fn () => $this->getOwnerRecord()->status === 'draft')
                    ->using(fn (array $data) => AdminWorkflowAdapter::record('admin.programmes.surveyQuestions.create', $data, ['survey' => $this->getOwnerRecord()->id])),
            ]);
    }
}
