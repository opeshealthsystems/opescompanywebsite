<?php
namespace App\Filament\Resources\LeadResource\RelationManagers;

use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class QuotesRelationManager extends RelationManager
{
    protected static string $relationship = 'quotes';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(250)->columnSpanFull(),
            Forms\Components\Select::make('status')
                ->options(Quote::statusOptions())->default('draft')->required(),
            Forms\Components\DatePicker::make('valid_until')->native(false)->nullable(),
            Forms\Components\TextInput::make('total')->numeric()->disabled()->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->fontFamily('mono'),
                Tables\Columns\TextColumn::make('title')->limit(30)->weight('semibold'),
                Tables\Columns\TextColumn::make('total')
                    ->getStateUsing(fn (Quote $record) => $record->formatTotal()),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'accepted'=>'success','draft'=>'gray','sent'=>'info',
                        'rejected'=>'danger','expired'=>'warning', default=>'gray',
                    }),
                Tables\Columns\TextColumn::make('valid_until')->date('d M Y')->placeholder('—'),
            ])
            ->defaultSort('created_at','desc')
            ->headerActions([
                Tables\Actions\Action::make('new_quote')
                    ->label('New Quote')
                    ->url(fn () => \App\Filament\Resources\QuoteResource::getUrl('create', ['lead_id' => $this->getOwnerRecord()->id]))
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Quote $record) => \App\Filament\Resources\QuoteResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
