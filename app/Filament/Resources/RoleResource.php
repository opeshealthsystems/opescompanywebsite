<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Roles & Permissions';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Role Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->disabled(),
                    Forms\Components\CheckboxList::make('permissions')
                        ->relationship('permissions', 'name')
                        ->columns(3)
                        ->disabled(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'super_admin' => 'danger',
                        'admin'       => 'warning',
                        'support'     => 'info',
                        'tester'      => 'success',
                        'customer'    => 'gray',
                        default       => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->badge()
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Role Details')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('name')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'super_admin' => 'danger',
                            'admin'       => 'warning',
                            'support'     => 'info',
                            'tester'      => 'success',
                            'customer'    => 'gray',
                            default       => 'gray',
                        }),
                    Infolists\Components\TextEntry::make('guard_name')->label('Guard')->badge()->color('gray'),
                    Infolists\Components\TextEntry::make('permissions_count')
                        ->label('Permission Count')
                        ->state(fn ($record) => $record->permissions()->count()),
                    Infolists\Components\TextEntry::make('users_count')
                        ->label('Users Assigned')
                        ->state(fn ($record) => $record->users()->count()),
                    Infolists\Components\TextEntry::make('permissions_list')
                        ->label('Permissions')
                        ->state(fn ($record) => $record->permissions->pluck('name')->sort()->values()->all())
                        ->badge()
                        ->separator(', ')
                        ->placeholder('No permissions assigned')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'view'  => Pages\ViewRole::route('/{record}'),
        ];
    }
}
