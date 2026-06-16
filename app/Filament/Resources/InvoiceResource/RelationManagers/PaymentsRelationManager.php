<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Models\InvoicePayment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';
    protected static ?string $title = 'Payment History';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('amount')
                ->numeric()
                ->required()
                ->minValue(0.01)
                ->label('Amount'),

            Forms\Components\Select::make('payment_method')
                ->options(InvoicePayment::methodOptions())
                ->default('bank_transfer')
                ->required(),

            Forms\Components\DatePicker::make('payment_date')
                ->required()
                ->default(today())
                ->native(false),

            Forms\Components\TextInput::make('reference_number')
                ->label('Reference / Transaction ID')
                ->maxLength(150)
                ->nullable(),

            Forms\Components\Hidden::make('recorded_by')
                ->default(fn () => auth()->id()),

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
                Tables\Columns\TextColumn::make('payment_date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->getStateUsing(fn (InvoicePayment $record) =>
                        ($record->invoice?->currency ?? 'XAF') . ' ' . number_format((float) $record->amount, 0)
                    )
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => InvoicePayment::methodOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('reference_number')
                    ->placeholder('—')
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('recorder.name')
                    ->label('Recorded By')
                    ->toggleable(),
            ])
            ->defaultSort('payment_date', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(fn ($record) => $record->invoice->reconcilePaymentStatus()),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->after(fn ($record) => $record->invoice->reconcilePaymentStatus()),
            ]);
    }
}
