<?php

namespace App\Filament\Resources\DeveloperTaskResource\RelationManagers;

use App\Models\Retest;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RetestsRelationManager extends RelationManager
{
    protected static string $relationship = 'retests';
    protected static ?string $title = 'Retests';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('result')->badge()
                    ->formatStateUsing(fn ($state) => Retest::resultOptions()[$state] ?? $state)
                    ->color(fn ($state) => $state === 'passed' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('cohortMember.user.name')->label('By')->placeholder('—'),
                Tables\Columns\TextColumn::make('notes')->limit(60),
                Tables\Columns\TextColumn::make('retested_at')->dateTime(),
            ])
            ->defaultSort('retested_at', 'desc');
    }
}
