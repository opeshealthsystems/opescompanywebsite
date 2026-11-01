<?php
namespace App\Filament\Resources\ValidationWorkflowResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TestCasesRelationManager extends RelationManager
{
    protected static string $relationship = 'testCases';

    protected function getTableQuery(): Builder
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::query(
            \App\Contexts\Validation\Application\Queries\AdminValidationCatalogQueries::class,
            'testCaseQuery',
            [$this->getOwnerRecord()],
        );
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
            Forms\Components\Textarea::make('description')->nullable()->columnSpanFull(),
            Forms\Components\Textarea::make('steps')->nullable()->columnSpanFull(),
            Forms\Components\Textarea::make('expected_result')->nullable()->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50)->weight('semibold'),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->using(fn (array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record(
                    'admin.validation.testCases.create', $data, ['workflow' => $this->getOwnerRecord()],
                )),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->using(fn (\App\Models\ValidationTestCase $record, array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record(
                    'admin.validation.testCases.update', $data, ['workflow' => $this->getOwnerRecord(), 'testCase' => $record],
                )),
                Tables\Actions\DeleteAction::make()->action(fn (\App\Models\ValidationTestCase $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run(
                    'admin.validation.testCases.delete', parameters: ['workflow' => $this->getOwnerRecord(), 'testCase' => $record],
                )),
            ]);
    }
}
