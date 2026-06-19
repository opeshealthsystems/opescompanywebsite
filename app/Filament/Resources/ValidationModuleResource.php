<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ValidationModuleResource\Pages;
use App\Filament\Resources\ValidationModuleResource\RelationManagers;
use App\Models\ValidationModule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ValidationModuleResource extends Resource
{
    protected static ?string $model = ValidationModule::class;
    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('validation_product_id')
                ->relationship('product', 'name')
                ->required(),
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('code')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->nullable()->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->weight('semibold'),
                Tables\Columns\TextColumn::make('code')->badge(),
                Tables\Columns\TextColumn::make('product.name')->label('Product'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\WorkflowsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListValidationModules::route('/'),
            'edit'  => Pages\EditValidationModule::route('/{record}/edit'),
        ];
    }
}
