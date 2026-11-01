<?php
namespace App\Filament\Resources\ValidationProductResource\RelationManagers;

use App\Filament\Resources\ValidationModuleResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ModulesRelationManager extends RelationManager
{
    protected static string $relationship = 'modules';

    protected function getTableQuery(): Builder
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::query(
            \App\Contexts\Validation\Application\Queries\AdminValidationCatalogQueries::class,
            'moduleQuery',
        )->where('validation_product_id', $this->getOwnerRecord()->getKey());
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
                Tables\Columns\TextColumn::make('workflows_count')->counts('workflows')->label('Workflows'),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()->using(fn (array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.validation.modules.create', $data, ['product' => $this->getOwnerRecord()->id]))])
            ->actions([
                Tables\Actions\EditAction::make()->using(fn (\App\Models\ValidationModule $record, array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.validation.modules.update', $data, ['module' => $record->id])),
                Tables\Actions\DeleteAction::make()->using(function (\App\Models\ValidationModule $record) { \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.validation.modules.delete', parameters: ['module' => $record->id]); return true; }),
                Tables\Actions\Action::make('manage_workflows')
                    ->label('Workflows')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->url(fn (\App\Models\ValidationModule $record) => ValidationModuleResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
