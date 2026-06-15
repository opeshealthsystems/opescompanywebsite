<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Personal Information')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignorable: fn (?User $record) => $record)
                        ->maxLength(150),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(30),
                    Forms\Components\FileUpload::make('avatar')
                        ->image()
                        ->directory('avatars')
                        ->nullable(),
                ]),

            Forms\Components\Section::make('Employment')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('employee_id')
                        ->label('Employee ID')
                        ->default(function () {
                            $year = date('Y');
                            $last = User::whereNotNull('employee_id')
                                ->where('employee_id', 'like', "EMP-{$year}-%")
                                ->max('employee_id');
                            $next = $last ? ((int) substr($last, -4)) + 1 : 1;
                            return 'EMP-' . $year . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
                        })
                        ->unique(ignorable: fn (?User $record) => $record)
                        ->nullable()
                        ->helperText('Auto-filled for staff. Leave blank for customer accounts.'),
                    Forms\Components\DatePicker::make('hire_date')
                        ->nullable(),
                    Forms\Components\TextInput::make('department')
                        ->maxLength(80)
                        ->nullable(),
                    Forms\Components\TextInput::make('position')
                        ->maxLength(80)
                        ->nullable(),
                ]),

            Forms\Components\Section::make('Account & Roles')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context) => $context === 'create')
                        ->minLength(8)
                        ->maxLength(64),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                    Forms\Components\CheckboxList::make('roles')
                        ->relationship('roles', 'name')
                        ->columns(2)
                        ->columnSpanFull()
                        ->searchable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->defaultImageUrl(fn (User $u) => 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=00C896&color=fff')
                    ->size(36),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('EMP ID')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('department')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(','),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (User $record) => $record->id === auth()->id()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view'   => Pages\ViewUser::route('/{record}'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('roles');
    }
}
