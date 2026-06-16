<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\EmployeeProfile;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Employees';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'employees';
    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['roles', 'employeeProfile'])
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'admin', 'support', 'tester']));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Personal')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(100),
                Forms\Components\TextInput::make('email')->email()->required()
                    ->unique(table: 'users', ignorable: fn (?User $record) => $record),
                Forms\Components\TextInput::make('phone')->tel()->maxLength(30)->nullable(),
                Forms\Components\TextInput::make('password')
                    ->password()->revealable()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context) => $context === 'create')
                    ->minLength(8),
            ]),

            Forms\Components\Section::make('Employment')->columns(2)->schema([
                Forms\Components\TextInput::make('employee_id')->label('Employee ID')
                    ->unique(table: 'users', ignorable: fn (?User $record) => $record)->nullable(),
                Forms\Components\Select::make('roles')->label('Role')
                    ->relationship('roles', 'name')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin'       => 'Admin',
                        'support'     => 'Support',
                        'tester'      => 'Tester',
                    ])
                    ->multiple(),
                Forms\Components\TextInput::make('department')->maxLength(80)->nullable(),
                Forms\Components\TextInput::make('position')->maxLength(80)->nullable(),
                Forms\Components\DatePicker::make('hire_date')->nullable(),
                Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
            ]),

            Forms\Components\Section::make('HR Details')->columns(2)
                ->relationship('employeeProfile')
                ->schema([
                    Forms\Components\Select::make('employment_type')
                        ->options(EmployeeProfile::employmentTypeOptions())
                        ->default('full_time')->required(),
                    Forms\Components\DatePicker::make('contract_end_date')
                        ->label('Contract End')->nullable(),
                    Forms\Components\TextInput::make('salary')->numeric()->nullable()
                        ->prefix(fn (Forms\Get $get) => $get('currency') ?: 'XAF'),
                    Forms\Components\Select::make('currency')
                        ->options(['XAF' => 'XAF', 'USD' => 'USD', 'EUR' => 'EUR'])
                        ->default('XAF'),
                    Forms\Components\TextInput::make('bank_name')->maxLength(100)->nullable(),
                    Forms\Components\TextInput::make('bank_account')->maxLength(60)->nullable(),
                    Forms\Components\TextInput::make('tax_id')->label('Tax ID')->maxLength(60)->nullable(),
                ]),

            Forms\Components\Section::make('Emergency Contact')->columns(2)
                ->relationship('employeeProfile')
                ->schema([
                    Forms\Components\TextInput::make('emergency_contact_name')->label('Name')->maxLength(100)->nullable(),
                    Forms\Components\TextInput::make('emergency_contact_phone')->label('Phone')->tel()->maxLength(30)->nullable(),
                    Forms\Components\TextInput::make('emergency_contact_relation')->label('Relation')->maxLength(60)->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()->size(36)
                    ->defaultImageUrl(fn (User $u) => 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=00C896&color=fff'),

                Tables\Columns\TextColumn::make('employee_id')
                    ->label('ID')->sortable()->placeholder('—'),

                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('semibold'),

                Tables\Columns\TextColumn::make('department')->placeholder('—'),

                Tables\Columns\TextColumn::make('position')->placeholder('—'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')->badge()->separator(','),

                Tables\Columns\TextColumn::make('employeeProfile.employment_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => EmployeeProfile::employmentTypeOptions()[$state] ?? $state)
                    ->badge()->color('info')->placeholder('—'),

                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),

                Tables\Columns\TextColumn::make('hire_date')
                    ->label('Hired')->date('d M Y')->sortable()->placeholder('—'),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin'       => 'Admin',
                        'support'     => 'Support',
                        'tester'      => 'Tester',
                    ])
                    ->query(fn ($query, $data) => $data['value'] ? $query->role($data['value']) : $query),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
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
            Infolists\Components\Section::make('Personal')->columns(3)->schema([
                Infolists\Components\TextEntry::make('name')->weight('bold'),
                Infolists\Components\TextEntry::make('email')->copyable(),
                Infolists\Components\TextEntry::make('phone')->placeholder('—'),
                Infolists\Components\TextEntry::make('employee_id')->label('EMP ID')
                    ->fontFamily('mono')->placeholder('—'),
                Infolists\Components\TextEntry::make('department')->placeholder('—'),
                Infolists\Components\TextEntry::make('position')->placeholder('—'),
            ]),

            Infolists\Components\Section::make('Employment')->columns(3)->schema([
                Infolists\Components\TextEntry::make('roles.name')
                    ->label('Role')->badge()->separator(', '),
                Infolists\Components\TextEntry::make('hire_date')
                    ->label('Hire Date')->date('d M Y')->placeholder('—'),
                Infolists\Components\IconEntry::make('is_active')->label('Active')->boolean(),

                Infolists\Components\TextEntry::make('employeeProfile.employment_type')
                    ->label('Employment Type')
                    ->formatStateUsing(fn ($state) => EmployeeProfile::employmentTypeOptions()[$state] ?? '—'),
                Infolists\Components\TextEntry::make('employeeProfile.contract_end_date')
                    ->label('Contract End')->date('d M Y')->placeholder('—'),
                Infolists\Components\TextEntry::make('employeeProfile.salary')
                    ->label('Salary')
                    ->formatStateUsing(fn ($state, $record) =>
                        $state ? ($record->employeeProfile?->currency . ' ' . number_format((float)$state, 0)) : '—'
                    ),
            ]),

            Infolists\Components\Section::make('Payroll & Banking')
                ->columns(3)
                ->collapsed()->collapsible()
                ->schema([
                    Infolists\Components\TextEntry::make('employeeProfile.bank_name')
                        ->label('Bank')->placeholder('—'),
                    Infolists\Components\TextEntry::make('employeeProfile.bank_account')
                        ->label('Account #')->fontFamily('mono')->placeholder('—'),
                    Infolists\Components\TextEntry::make('employeeProfile.tax_id')
                        ->label('Tax ID')->fontFamily('mono')->placeholder('—'),
                ]),

            Infolists\Components\Section::make('Emergency Contact')
                ->columns(3)
                ->collapsed()->collapsible()
                ->schema([
                    Infolists\Components\TextEntry::make('employeeProfile.emergency_contact_name')
                        ->label('Name')->placeholder('—'),
                    Infolists\Components\TextEntry::make('employeeProfile.emergency_contact_phone')
                        ->label('Phone')->placeholder('—'),
                    Infolists\Components\TextEntry::make('employeeProfile.emergency_contact_relation')
                        ->label('Relation')->placeholder('—'),
                ]),
        ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'employee_id', 'department'];
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\EmployeeResource\RelationManagers\LeaveBalancesRelationManager::class,
            \App\Filament\Resources\EmployeeResource\RelationManagers\PerformanceReviewsRelationManager::class,
            \App\Filament\Resources\EmployeeResource\RelationManagers\TimesheetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view'   => Pages\ViewEmployee::route('/{record}'),
            'edit'   => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
