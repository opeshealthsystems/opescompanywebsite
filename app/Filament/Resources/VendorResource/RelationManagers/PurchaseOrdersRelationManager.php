<?php

namespace App\Filament\Resources\VendorResource\RelationManagers;

use App\Models\PurchaseOrder;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseOrders';
    protected static ?string $title = 'Purchase Orders';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved'  => 'success',
                        'pending'   => 'warning',
                        'received'  => 'info',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->getStateUsing(fn (PurchaseOrder $r) => $r->currency . ' ' . number_format((float) $r->total, 0))
                    ->label('Total')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expected_date')
                    ->date('d M Y')
                    ->label('Expected')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('download_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->url(fn (PurchaseOrder $record) => route('purchase-orders.pdf', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
