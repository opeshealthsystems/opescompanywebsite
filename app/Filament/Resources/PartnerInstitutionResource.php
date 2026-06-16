<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerInstitutionResource\Pages;
use App\Models\PartnerInstitution;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PartnerInstitutionResource extends Resource
{
    protected static ?string $model = PartnerInstitution::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 15;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(200),
            TextInput::make('name_fr')->label('Name (French)')->maxLength(200),
            Select::make('type')->options(PartnerInstitution::typeOptions())->required(),
            TextInput::make('country')->maxLength(80)->default('CM'),
            TextInput::make('city')->maxLength(80),
            TextInput::make('website')->url()->maxLength(300),
            FileUpload::make('logo')
                ->image()
                ->directory('partners')
                ->maxSize(1024),
            Textarea::make('description')->rows(3),
            Textarea::make('description_fr')->label('Description (French)')->rows(3),
            TextInput::make('partnership_since')->numeric()->minValue(1990)->maxValue(2030)->label('Partner Since (Year)'),
            TextInput::make('sort_order')->numeric()->default(0),
            Toggle::make('is_featured')->label('Featured on partnerships page'),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')->circular()->size(40),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => PartnerInstitution::typeOptions()[$state] ?? $state),
                TextColumn::make('country')->sortable(),
                ToggleColumn::make('is_featured')->label('Featured'),
                TextColumn::make('sort_order')->label('Order')->sortable(),
            ])
            ->reorderable('sort_order')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPartnerInstitutions::route('/'),
            'create' => Pages\CreatePartnerInstitution::route('/create'),
            'edit'   => Pages\EditPartnerInstitution::route('/{record}/edit'),
        ];
    }
}
