<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerProfileResource\Pages;
use App\Models\CustomerProfile;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerProfileResource extends Resource
{
    protected static ?string $model = CustomerProfile::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Customer Profiles';
    protected static ?string $recordTitleAttribute = 'facility_name';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Account')->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Customer Account')
                    ->options(fn () => User::role('customer')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->unique(ignoreRecord: true),
            ]),

            Forms\Components\Section::make('Facility Information')->schema([
                Forms\Components\TextInput::make('facility_name')
                    ->required()
                    ->maxLength(200),

                Forms\Components\Select::make('facility_type')
                    ->options([
                        'clinic'            => 'Clinic',
                        'hospital'          => 'Hospital',
                        'laboratory'        => 'Laboratory',
                        'pharmacy'          => 'Pharmacy',
                        'specialist_centre' => 'Specialist Centre',
                        'ngos'              => 'NGO / Non-profit',
                        'government'        => 'Government / Ministry',
                        'other'             => 'Other',
                    ])
                    ->nullable(),

                Forms\Components\TextInput::make('country')
                    ->maxLength(80)
                    ->nullable(),

                Forms\Components\TextInput::make('city')
                    ->maxLength(80)
                    ->nullable(),

                Forms\Components\Textarea::make('address')
                    ->rows(2)
                    ->nullable()
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('facility_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Account')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('facility_type')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state ? ucwords(str_replace('_', ' ', $state)) : '—'),

                Tables\Columns\TextColumn::make('country')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('city')
                    ->placeholder('—'),
            ])
            ->defaultSort('facility_name')
            ->filters([
                Tables\Filters\SelectFilter::make('facility_type')
                    ->options([
                        'clinic'            => 'Clinic',
                        'hospital'          => 'Hospital',
                        'laboratory'        => 'Laboratory',
                        'pharmacy'          => 'Pharmacy',
                        'specialist_centre' => 'Specialist Centre',
                        'ngos'              => 'NGO / Non-profit',
                        'government'        => 'Government / Ministry',
                        'other'             => 'Other',
                    ]),

                Tables\Filters\SelectFilter::make('country')
                    ->options(fn () => CustomerProfile::whereNotNull('country')
                        ->distinct()
                        ->pluck('country', 'country')
                        ->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Account')->schema([
                Infolists\Components\TextEntry::make('user.name')->label('Customer'),
                Infolists\Components\TextEntry::make('user.email')->label('Email'),
                Infolists\Components\TextEntry::make('user.phone')->label('Phone')->placeholder('—'),
            ])->columns(3),

            Infolists\Components\Section::make('Facility')->schema([
                Infolists\Components\TextEntry::make('facility_name')->label('Facility Name'),
                Infolists\Components\TextEntry::make('facility_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => $state ? ucwords(str_replace('_', ' ', $state)) : '—'),
                Infolists\Components\TextEntry::make('country')->placeholder('—'),
                Infolists\Components\TextEntry::make('city')->placeholder('—'),
                Infolists\Components\TextEntry::make('address')->placeholder('—')->columnSpanFull(),
            ])->columns(4),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['facility_name', 'city', 'country'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomerProfiles::route('/'),
            'create' => Pages\CreateCustomerProfile::route('/create'),
            'view'   => Pages\ViewCustomerProfile::route('/{record}'),
            'edit'   => Pages\EditCustomerProfile::route('/{record}/edit'),
        ];
    }
}
