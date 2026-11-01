<?php

namespace App\Filament\Resources\CohortResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TestCasesRelationManager extends RelationManager
{
    protected static string $relationship = 'testCases';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('workflow.name')->label('Workflow'),
                Tables\Columns\TextColumn::make('workflow.module.name')->label('Module'),
                Tables\Columns\TextColumn::make('workflow.module.product.name')->label('Product'),
                Tables\Columns\TextColumn::make('due_date')->date()->placeholder('—'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('attachTestCase')
                    ->label('Attach test case')
                    ->icon('heroicon-o-link')
                    ->form(fn () => [
                        Forms\Components\Select::make('validation_test_case_id')->label('Test case')
                            ->options(\App\Models\ValidationTestCase::query()->orderBy('title')->pluck('title', 'id'))->searchable()->required(),
                        Forms\Components\DatePicker::make('due_date')->native(false),
                    ])
                    ->action(function (RelationManager $livewire, array $data): void {
                        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.validation.cohortTestCases.attach', $data,
                            ['cohort' => $livewire->getOwnerRecord()->id]);
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()->action(function (RelationManager $livewire, \App\Models\ValidationTestCase $record): void {
                    \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.validation.cohortTestCases.detach', parameters: [
                        'cohort' => $livewire->getOwnerRecord()->id, 'testCase' => $record->id,
                    ]);
                }),
            ]);
    }
}
