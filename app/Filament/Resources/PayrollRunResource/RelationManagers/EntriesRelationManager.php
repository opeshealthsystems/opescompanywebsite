<?php

namespace App\Filament\Resources\PayrollRunResource\RelationManagers;

use App\Models\PayrollEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'entries';
    protected static ?string $title = 'Payroll Entries';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Employee Payroll')->columns(2)->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Employee')
                    ->options(fn () => \App\Models\User::whereHas('roles', fn ($q) =>
                        $q->whereIn('name', ['super_admin', 'admin', 'support', 'tester'])
                    )->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid'])
                    ->default('pending')
                    ->required(),

                Forms\Components\TextInput::make('gross_salary')
                    ->label('Gross Salary')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $totalDed = collect($get('deductions') ?? [])->sum('amount');
                        $set('total_deductions', $totalDed);
                        $set('net_salary', max(0, (float) $state - $totalDed));
                    }),

                Forms\Components\Select::make('currency')
                    ->options(['XAF' => 'XAF', 'USD' => 'USD', 'EUR' => 'EUR'])
                    ->default('XAF')
                    ->required(),
            ]),

            Forms\Components\Section::make('Deductions')->schema([
                Forms\Components\Repeater::make('deductions')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->label('Deduction Name')
                            ->required()
                            ->placeholder('e.g. CNPS, Health Insurance, Tax')
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->minValue(0),
                    ])
                    ->columns(3)
                    ->addActionLabel('Add Deduction')
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $totalDed = collect($state ?? [])->sum('amount');
                        $set('total_deductions', $totalDed);
                        $set('net_salary', max(0, (float) $get('gross_salary') - $totalDed));
                    })
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Net Pay')->columns(2)->schema([
                Forms\Components\TextInput::make('total_deductions')
                    ->label('Total Deductions')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(),

                Forms\Components\TextInput::make('net_salary')
                    ->label('Net Salary')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->helperText('Auto-computed from gross minus deductions, or override manually.'),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('gross_salary')
                    ->label('Gross')
                    ->getStateUsing(fn (PayrollEntry $r) => $r->currency . ' ' . number_format((float) $r->gross_salary, 0))
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_deductions')
                    ->label('Deductions')
                    ->getStateUsing(fn (PayrollEntry $r) => $r->currency . ' ' . number_format((float) $r->total_deductions, 0))
                    ->color('danger'),

                Tables\Columns\TextColumn::make('net_salary')
                    ->label('Net Pay')
                    ->getStateUsing(fn (PayrollEntry $r) => $r->formatNet())
                    ->weight('semibold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'    => 'success',
                        'pending' => 'warning',
                        default   => 'gray',
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function () {
                        $this->getOwnerRecord()->recalculateTotals();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function () {
                        $this->getOwnerRecord()->recalculateTotals();
                    }),

                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (PayrollEntry $record) => $record->status === 'paid')
                    ->action(function (PayrollEntry $record) {
                        $record->update(['status' => 'paid']);
                        Notification::make()->title('Marked as paid')->success()->send();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->after(function () {
                        $this->getOwnerRecord()->recalculateTotals();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_all_paid')
                        ->label('Mark All as Paid')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['status' => 'paid']);
                            Notification::make()->title('All entries marked as paid')->success()->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            $this->getOwnerRecord()->recalculateTotals();
                        }),
                ]),
            ]);
    }
}
