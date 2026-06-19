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
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action) => [
                        $action->getRecordSelect(),
                        Forms\Components\DatePicker::make('due_date')->native(false),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ]);
    }
}
