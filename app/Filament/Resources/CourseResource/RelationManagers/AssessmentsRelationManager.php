<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Models\CourseAssessment;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class AssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessments';
    protected static ?string $title = 'Exams & assessments';

    protected function getTableQuery(): Builder
    {
        return AdminWorkflowAdapter::query(
            \App\Contexts\Learning\Application\Queries\CourseAssessmentQueries::class,
            'adminQuery',
            [$this->getOwnerRecord()],
        );
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->required()->maxLength(200),
            TextInput::make('pass_mark')->label('Pass mark (%)')->numeric()->minValue(1)->maxValue(100)->default(70)->required(),
            TextInput::make('attempt_limit')->label('Attempt limit (blank = unlimited)')->numeric()->minValue(1)->maxValue(100),
            TextInput::make('duration_minutes')->label('Time limit in minutes (blank = none)')->numeric()->minValue(1)->maxValue(1440),
            Textarea::make('instructions')->rows(3)->columnSpanFull(),
            Toggle::make('is_published')->label('Published to enrolled practitioners')->default(false),
            Repeater::make('questions')->label('Questions')->required()->minItems(1)->maxItems(100)->reorderable(false)->collapsible()->columnSpanFull()
                ->schema([
                    TextInput::make('id')->label('Question key')->helperText('Unique within this exam, for example q1.')->required()->maxLength(80),
                    TextInput::make('points')->numeric()->minValue(1)->maxValue(100)->default(1)->required(),
                    Textarea::make('prompt')->rows(3)->required()->maxLength(3000)->columnSpanFull(),
                    Repeater::make('choices')->required()->minItems(2)->maxItems(8)->reorderable(false)->schema([
                        TextInput::make('id')->label('Choice key')->helperText('Unique in this question, for example A.')->required()->maxLength(40),
                        TextInput::make('label')->required()->maxLength(1000),
                    ])->columns(2)->columnSpanFull(),
                    TextInput::make('correct_choice_id')->label('Correct choice key')->helperText('Must match one of this question’s choice keys.')->required()->maxLength(40),
                    Textarea::make('explanation')->label('Answer explanation')->rows(2)->maxLength(3000)->columnSpanFull(),
                ])->columns(2),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->wrap(),
            TextColumn::make('pass_mark')->label('Pass mark')->suffix('%')->sortable(),
            IconColumn::make('is_published')->boolean(),
            TextColumn::make('attempts_count')->label('Attempts')->sortable(),
        ])->defaultSort('id', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()->using(fn (array $data) => AdminWorkflowAdapter::record(
                    'admin.learning.assessments.create', $data, ['course' => $this->getOwnerRecord()->id],
                )),
            ])->actions([
                Tables\Actions\EditAction::make()->using(fn (CourseAssessment $record, array $data) => AdminWorkflowAdapter::record(
                    'admin.learning.assessments.update', $data, ['course' => $this->getOwnerRecord()->id, 'assessment' => $record->id],
                )),
                Tables\Actions\DeleteAction::make()->using(function (CourseAssessment $record) {
                    AdminWorkflowAdapter::run('admin.learning.assessments.delete', parameters: ['course' => $this->getOwnerRecord()->id, 'assessment' => $record->id]);
                    return true;
                }),
            ]);
    }
}
