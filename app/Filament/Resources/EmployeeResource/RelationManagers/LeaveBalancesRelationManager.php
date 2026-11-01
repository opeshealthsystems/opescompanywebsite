<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaveBalancesRelationManager extends RelationManager
{
    protected static string $relationship = 'leaveBalances';
    protected static ?string $title = 'Leave Balances';

    protected function getTableQuery(): Builder
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::query(
            \App\Contexts\Workforce\Application\Queries\AdminLeaveQueries::class,
            'leaveBalanceQuery',
            [$this->getOwnerRecord()],
        );
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('year')
                ->options(array_combine(
                    range(now()->year - 1, now()->year + 2),
                    range(now()->year - 1, now()->year + 2)
                ))
                ->default(now()->year)
                ->disabled(fn (string $operation) => $operation === 'edit')
                ->dehydrated(fn (string $operation) => $operation === 'create')
                ->required(),

            Forms\Components\Select::make('type')
                ->options(LeaveRequest::typeOptions())
                ->disabled(fn (string $operation) => $operation === 'edit')
                ->dehydrated(fn (string $operation) => $operation === 'create')
                ->required(),

            Forms\Components\TextInput::make('entitled_days')
                ->label('Entitled Days')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required()
                ->helperText('Total days allowed for the year'),

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
                Tables\Actions\CreateAction::make()->using(fn (array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.workforce.leaveBalances.create', $data, ['employee' => $this->getOwnerRecord()->id])),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->using(fn (LeaveBalance $record, array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.workforce.leaveBalances.update', $data, ['leaveBalance' => $record->id])),
                Tables\Actions\DeleteAction::make()->using(function (LeaveBalance $record) { \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.workforce.leaveBalances.delete', parameters: ['leaveBalance' => $record->id]); return true; }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->using(function ($records) { foreach ($records as $record) \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.workforce.leaveBalances.delete', parameters: ['leaveBalance' => $record->id]); }),
                ]),
            ]);
    }
}
