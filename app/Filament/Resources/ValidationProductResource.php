<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ValidationProductResource\Pages;
use App\Filament\Resources\ValidationProductResource\RelationManagers;
use App\Models\ValidationProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ValidationProductResource extends Resource
{
    protected static ?string $model = ValidationProduct::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Validation Catalog';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('code')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('snake_case unique code'),
            Forms\Components\Textarea::make('description')->nullable()->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('semibold'),
                Tables\Columns\TextColumn::make('code')->badge(),
                Tables\Columns\TextColumn::make('modules_count')->counts('modules')->label('Modules'),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->defaultSort('name')
            ->actions([
                Tables\Actions\EditAction::make()->using(fn (\App\Models\ValidationProduct $record, array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.validation.products.update', $data, ['validationProduct' => $record->id])),
                Tables\Actions\DeleteAction::make()->using(function (ValidationProduct $record) {
                    \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.validation.products.delete', parameters: ['validationProduct' => $record->id]);
                    return true;
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->using(function ($records) {
                        foreach ($records as $record) {
                            \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.validation.products.delete', parameters: ['validationProduct' => $record->id]);
                        }
                    }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ModulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListValidationProducts::route('/'),
            'create' => Pages\CreateValidationProduct::route('/create'),
            'edit'   => Pages\EditValidationProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return app(\App\Contexts\Validation\Application\Queries\AdminValidationProductQueries::class)->query(
            new \App\Contexts\Shared\Application\WorkflowInput(actor: auth()->user(), language: app()->getLocale())
        );
    }
}
