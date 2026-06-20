<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

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
                        ->searchable()
                        // Role assignment is a super_admin privilege (admin is deliberately
                        // denied manage_roles). Hidden — and therefore never dehydrated or
                        // synced — for everyone else, so an admin cannot grant super_admin.
                        ->visible(fn () => auth()->user()?->hasRole('super_admin') ?? false),
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
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin'       => 'Admin',
                        'support'     => 'Support',
                        'tester'      => 'Tester',
                        'customer'    => 'Customer',
                    ])
                    ->query(fn ($query, $data) => $data['value'] ? $query->role($data['value']) : $query),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('send_welcome')
                    ->label('Send Welcome')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalDescription(fn (User $record) => "Send a welcome email to {$record->email}?")
                    ->action(function (User $record) {
                        Mail::to($record->email)->queue(new WelcomeEmail($record));
                        $record->notify(new \App\Notifications\FeedEntry(
                            'account.welcome',
                            'Welcome to OPES',
                            'Welcome to OPES Health Systems.',
                            'sparkles',
                            null,
                        ));
                        Notification::make()->title('Welcome email queued')->success()->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (User $record) => $record->id === auth()->id()),
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
            Infolists\Components\Section::make('Personal')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('name'),
                    Infolists\Components\TextEntry::make('email')->copyable(),
                    Infolists\Components\TextEntry::make('phone')->placeholder('—'),
                    Infolists\Components\TextEntry::make('created_at')->label('Member Since')->dateTime('d M Y'),
                ]),

            Infolists\Components\Section::make('Employment')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('employee_id')
                        ->label('EMP ID')
                        ->fontFamily(\Filament\Support\Enums\FontFamily::Mono)
                        ->placeholder('—'),
                    Infolists\Components\TextEntry::make('hire_date')->label('Hire Date')->date('d M Y')->placeholder('—'),
                    Infolists\Components\TextEntry::make('department')->placeholder('—'),
                    Infolists\Components\TextEntry::make('position')->placeholder('—'),
                ]),

            Infolists\Components\Section::make('Account')
                ->columns(2)
                ->schema([
                    Infolists\Components\IconEntry::make('is_active')->label('Active')->boolean(),
                    Infolists\Components\TextEntry::make('roles.name')
                        ->label('Roles')
                        ->badge()
                        ->separator(', ')
                        ->placeholder('No roles assigned'),
                ]),
        ]);
    }

    public static function getRelationManagers(): array
    {
        return [
            \App\Filament\Resources\UserResource\RelationManagers\EmployeeProfileRelationManager::class,
        ];
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
