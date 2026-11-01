<?php
namespace App\Filament\Resources\ValidationModuleResource\RelationManagers;

use App\Filament\Resources\ValidationWorkflowResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkflowsRelationManager extends RelationManager
{
    protected static string $relationship = 'workflows';

    protected function getTableQuery(): Builder
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::query(
            \App\Contexts\Validation\Application\Queries\AdminValidationCatalogQueries::class,
            'workflowQuery',
            [$this->getOwnerRecord()],
        );
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('code')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->nullable()->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->weight('semibold'),
                Tables\Columns\TextColumn::make('code')->badge(),
                Tables\Columns\TextColumn::make('test_cases_count')->counts('testCases')->label('Test Cases'),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->using(fn (array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record(
                    'admin.validation.workflows.create', $data, ['module' => $this->getOwnerRecord()],
                )),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->using(fn (\App\Models\ValidationWorkflow $record, array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record(
                    'admin.validation.workflows.update', $data, ['module' => $this->getOwnerRecord(), 'workflow' => $record],
                )),
                Tables\Actions\DeleteAction::make()->action(fn (\App\Models\ValidationWorkflow $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run(
                    'admin.validation.workflows.delete', parameters: ['module' => $this->getOwnerRecord(), 'workflow' => $record],
                )),
                Tables\Actions\Action::make('manage_test_cases')
                    ->label('Test Cases')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->url(fn (\App\Models\ValidationWorkflow $record) => ValidationWorkflowResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
