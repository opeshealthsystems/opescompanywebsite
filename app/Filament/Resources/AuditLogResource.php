<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Audit Log';
    protected static ?string $navigationGroup = 'Platform';
    protected static ?int $navigationSort = 98;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canCreate(): bool { return false; }

    public static function form(Form $form): Form { return $form->schema([]); }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('When')->dateTime('d M Y H:i:s')->sortable()->fontFamily('mono'),
                Tables\Columns\TextColumn::make('user.name')->label('User')->placeholder('System')->searchable(),
                Tables\Columns\TextColumn::make('action')->badge()
                    ->color(fn ($state) => match($state) {
                        'created'=>'success','updated'=>'info','deleted'=>'danger', default=>'gray',
                    }),
                Tables\Columns\TextColumn::make('model_type')->label('Resource')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('model_id')->label('ID')->fontFamily('mono')->sortable(),
                Tables\Columns\TextColumn::make('ip_address')->label('IP')->toggleable()->fontFamily('mono'),
            ])
            ->defaultSort('created_at','desc')
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options(['created'=>'Created','updated'=>'Updated','deleted'=>'Deleted']),
                Tables\Filters\SelectFilter::make('model_type')
                    ->options(fn () => AuditLog::distinct()->orderBy('model_type')->pluck('model_type','model_type')->toArray())
                    ->label('Resource Type'),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user','name'),
            ])
            ->actions([Tables\Actions\ViewAction::make()])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Event')->columns(3)->schema([
                Infolists\Components\TextEntry::make('created_at')->label('When')->dateTime('d M Y H:i:s')->fontFamily('mono'),
                Infolists\Components\TextEntry::make('user.name')->label('User')->placeholder('System'),
                Infolists\Components\TextEntry::make('action')->badge()
                    ->color(fn ($state) => match($state) {
                        'created'=>'success','updated'=>'info','deleted'=>'danger', default=>'gray',
                    }),
                Infolists\Components\TextEntry::make('model_type')->label('Resource')->badge()->color('gray'),
                Infolists\Components\TextEntry::make('model_id')->label('Record ID')->fontFamily('mono'),
                Infolists\Components\TextEntry::make('ip_address')->label('IP Address')->fontFamily('mono')->placeholder('—'),
            ]),
            Infolists\Components\Section::make('Before')->collapsible()->schema([
                Infolists\Components\KeyValueEntry::make('old_values')->label('Previous Values')->placeholder('—')->columnSpanFull(),
            ]),
            Infolists\Components\Section::make('After')->collapsible()->schema([
                Infolists\Components\KeyValueEntry::make('new_values')->label('New Values')->placeholder('—')->columnSpanFull(),
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
