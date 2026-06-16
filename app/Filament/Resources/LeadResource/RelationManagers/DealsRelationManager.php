<?php
namespace App\Filament\Resources\LeadResource\RelationManagers;

use App\Models\Deal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DealsRelationManager extends RelationManager
{
    protected static string $relationship = 'deals';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(250),
            Forms\Components\Select::make('stage')->options(Deal::stageOptions())->default('prospecting')->required(),
            Forms\Components\TextInput::make('value')->numeric()->default(0)->minValue(0),
            Forms\Components\TextInput::make('probability')->numeric()->default(50)->suffix('%'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->fontFamily('mono'),
                Tables\Columns\TextColumn::make('title')->limit(30)->weight('semibold'),
                Tables\Columns\TextColumn::make('stage')->badge()
                    ->color(fn ($state) => match($state) {
                        'closed_won'=>'success','closed_lost'=>'danger','negotiation'=>'warning',
                        'proposal'=>'info', default=>'gray',
                    })
                    ->formatStateUsing(fn ($state) => Deal::stageOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('value')
                    ->getStateUsing(fn (Deal $record) => $record->formatValue()),
                Tables\Columns\TextColumn::make('probability')->suffix('%'),
            ])
            ->defaultSort('created_at','desc')
            ->headerActions([
                Tables\Actions\Action::make('new_deal')
                    ->label('New Deal')
                    ->url(fn () => \App\Filament\Resources\DealResource::getUrl('create', ['lead_id' => $this->getOwnerRecord()->id]))
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Deal $record) => \App\Filament\Resources\DealResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
