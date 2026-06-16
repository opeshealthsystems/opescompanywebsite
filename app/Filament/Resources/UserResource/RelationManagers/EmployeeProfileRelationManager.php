<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\EmployeeProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeProfileRelationManager extends RelationManager
{
    protected static string $relationship = 'employeeProfile';
    protected static ?string $title = 'Employee Profile';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Employment')->columns(2)->schema([
                Forms\Components\Select::make('employment_type')
                    ->options(EmployeeProfile::employmentTypeOptions())
                    ->required(),

                Forms\Components\DatePicker::make('contract_end_date')
                    ->label('Contract End Date')
                    ->nullable(),
            ]),

            Forms\Components\Section::make('Compensation')->columns(2)->schema([
                Forms\Components\TextInput::make('salary')
                    ->numeric()
                    ->minValue(0)
                    ->nullable(),

                Forms\Components\Select::make('currency')
                    ->options(['XAF' => 'XAF', 'USD' => 'USD', 'EUR' => 'EUR'])
                    ->default('XAF'),
            ]),

            Forms\Components\Section::make('Banking')->columns(2)->schema([
                Forms\Components\TextInput::make('bank_name')
                    ->label('Bank Name')
                    ->nullable(),

                Forms\Components\TextInput::make('bank_account')
                    ->label('Account Number')
                    ->nullable(),

                Forms\Components\TextInput::make('tax_id')
                    ->label('Tax ID')
                    ->nullable(),
            ]),

            Forms\Components\Section::make('Emergency Contact')->columns(3)->schema([
                Forms\Components\TextInput::make('emergency_contact_name')
                    ->label('Name')
                    ->nullable(),

                Forms\Components\TextInput::make('emergency_contact_phone')
                    ->label('Phone')
                    ->nullable(),

                Forms\Components\TextInput::make('emergency_contact_relation')
                    ->label('Relationship')
                    ->nullable(),
            ]),

            Forms\Components\Textarea::make('notes')
                ->rows(2)
                ->nullable()
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employment_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => EmployeeProfile::employmentTypeOptions()[$state] ?? $state)
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('salary')
                    ->label('Salary')
                    ->getStateUsing(fn (EmployeeProfile $r) => $r->currency . ' ' . number_format((float) $r->salary, 0))
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('contract_end_date')
                    ->label('Contract End')
                    ->date('d M Y')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Bank')
                    ->placeholder('—'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
