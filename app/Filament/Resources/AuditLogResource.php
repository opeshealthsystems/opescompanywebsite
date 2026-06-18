<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Audit Logs';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 99;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function canCreate(): bool { return false; }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();
        return $count > 0 ? number_format($count) : null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'gray'; }

    private static function actionColor(string $state): string
    {
        return match ($state) {
            'created'                    => 'success',
            'updated'                    => 'info',
            'deleted', 'force_deleted'   => 'danger',
            'login'                      => 'warning',
            'logout'                     => 'gray',
            'approved'                   => 'success',
            'rejected'                   => 'danger',
            default                      => 'gray',
        };
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('When')
                    ->since()
                    ->sortable()
                    ->fontFamily('mono')
                    ->tooltip(fn ($record) => $record->created_at?->format('Y-m-d H:i:s')),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('System')
                    ->searchable(),

                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => self::actionColor($state))
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('model_type')
                    ->label('Resource')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('model_id')
                    ->label('ID')
                    ->fontFamily('mono')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->fontFamily('mono')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options(fn () => AuditLog::distinct()
                        ->orderBy('action')
                        ->pluck('action', 'action')
                        ->mapWithKeys(fn ($v) => [$v => ucfirst(str_replace('_', ' ', $v))])
                        ->toArray()
                    ),

                Tables\Filters\SelectFilter::make('model_type')
                    ->label('Resource Type')
                    ->options(fn () => AuditLog::distinct()
                        ->orderBy('model_type')
                        ->pluck('model_type', 'model_type')
                        ->toArray()
                    ),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('date_range')
                    ->label('Date range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'],  fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
                            ->when($data['until'], fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
                    }),
            ])
            ->actions([Tables\Actions\ViewAction::make()])
            ->bulkActions([])
            ->paginated([25, 50, 100, 250]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Event')->columns(3)->schema([
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('d M Y H:i:s')
                    ->fontFamily('mono'),

                Infolists\Components\TextEntry::make('action')
                    ->badge()
                    ->color(fn (string $state): string => self::actionColor($state))
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                Infolists\Components\TextEntry::make('user.name')
                    ->label('Performed by')
                    ->placeholder('System'),

                Infolists\Components\TextEntry::make('model_type')->label('Resource')->badge()->color('gray'),
                Infolists\Components\TextEntry::make('model_id')->label('Record ID')->fontFamily('mono'),
                Infolists\Components\TextEntry::make('ip_address')->label('IP Address')->fontFamily('mono')->placeholder('—'),

                Infolists\Components\TextEntry::make('user_agent')
                    ->label('User Agent')
                    ->columnSpanFull()
                    ->placeholder('—'),
            ]),

            Infolists\Components\Section::make('Before (previous values)')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Infolists\Components\KeyValueEntry::make('old_values')
                        ->label('')
                        ->placeholder('No previous values')
                        ->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('After (new values)')
                ->collapsible()
                ->schema([
                    Infolists\Components\KeyValueEntry::make('new_values')
                        ->label('')
                        ->placeholder('No values recorded')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view'  => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}
