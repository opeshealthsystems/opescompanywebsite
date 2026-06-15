<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentLeadsWidget extends BaseWidget
{
    protected static ?string $heading = 'New Demo Requests';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lead::whereIn('status', ['new', 'contacted'])
                    ->orderByDesc('created_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('facility_type')
                    ->label('Facility')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('products')
                    ->label('Interested In')
                    ->limit(35)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'new'       => 'danger',
                        'contacted' => 'warning',
                        'qualified' => 'success',
                        'closed'    => 'gray',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('source')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Lead $record): string => LeadResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->size('sm'),
            ])
            ->defaultPaginationPageOption(8)
            ->paginated([8, 16, 24]);
    }
}
