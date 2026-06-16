<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveBalancesRelationManager extends RelationManager
{
    protected static string $relationship = 'leaveBalances';
    protected static ?string $title = 'Leave Balances';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('year')
                ->options(array_combine(
                    range(now()->year - 1, now()->year + 2),
                    range(now()->year - 1, now()->year + 2)
                ))
                ->default(now()->year)
                ->required(),

            Forms\Components\Select::make('type')
                ->options(LeaveRequest::typeOptions())
                ->required(),

            Forms\Components\TextInput::make('entitled_days')
                ->label('Entitled Days')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required()
                ->helperText('Total days allowed for the year'),

            Forms\Components\TextInput::make('used_days')
                ->label('Used Days')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required()
                ->helperText('Days already used (auto-tracked from approved requests)'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => LeaveRequest::typeOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('entitled_days')
                    ->label('Entitled')
                    ->suffix(' days')
                    ->sortable(),

                Tables\Columns\TextColumn::make('used_days')
                    ->label('Used')
                    ->suffix(' days')
                    ->color('warning')
                    ->sortable(),

                Tables\Columns\TextColumn::make('remaining')
                    ->label('Remaining')
                    ->getStateUsing(fn (LeaveBalance $r) => max(0, (float) $r->entitled_days - (float) $r->used_days))
                    ->suffix(' days')
                    ->color(fn (LeaveBalance $r) =>
                        ((float) $r->entitled_days - (float) $r->used_days) <= 0 ? 'danger' : 'success'
                    ),
            ])
            ->defaultSort('year', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
