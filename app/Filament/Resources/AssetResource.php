<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;
    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $navigationLabel = 'Asset Register';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Asset Details')->columns(2)->schema([
                Forms\Components\TextInput::make('reference')->disabled()->placeholder('Auto-generated'),
                Forms\Components\Select::make('status')->options(Asset::statusOptions())->default('active')->required(),
                Forms\Components\TextInput::make('name')->required()->maxLength(200)->columnSpanFull(),
                Forms\Components\Select::make('category')->options(Asset::categoryOptions())->required(),
                Forms\Components\TextInput::make('brand')->maxLength(100)->nullable(),
                Forms\Components\TextInput::make('model')->maxLength(100)->nullable(),
                Forms\Components\TextInput::make('serial_number')->maxLength(100)->nullable(),
                Forms\Components\TextInput::make('location')->maxLength(150)->nullable(),
                Forms\Components\Select::make('assigned_to')
                    ->label('Assigned To')
                    ->options(fn () => User::orderBy('name')->pluck('name','id'))
                    ->searchable()->nullable(),
                Forms\Components\DatePicker::make('warranty_expires')->label('Warranty Expires')->native(false)->nullable(),
            ]),
            Forms\Components\Section::make('Financial')->columns(3)->schema([
                Forms\Components\DatePicker::make('purchase_date')->native(false)->nullable(),
                Forms\Components\TextInput::make('purchase_price')->numeric()->default(0)->minValue(0),
                Forms\Components\TextInput::make('current_value')->label('Current Value')->numeric()->default(0)->minValue(0),
                Forms\Components\Select::make('currency')->options(['XAF'=>'XAF','USD'=>'USD','EUR'=>'EUR'])->default('XAF'),
            ]),
            Forms\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Forms\Components\Textarea::make('notes')->rows(3)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->fontFamily('mono')->copyable()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->weight('semibold')->limit(35),
                Tables\Columns\TextColumn::make('category')->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => Asset::categoryOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'active' => 'success','in_repair' => 'warning','retired' => 'gray','disposed' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To')->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('serial_number')->label('Serial #')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('purchase_date')->label('Purchased')->date('d M Y')->placeholder('—')->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')->options(Asset::categoryOptions()),
                Tables\Filters\SelectFilter::make('status')->options(Asset::statusOptions()),
                Tables\Filters\SelectFilter::make('assigned_to')->label('Assigned To')
                    ->options(fn () => User::orderBy('name')->pluck('name','id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Asset')->columns(3)->schema([
                Infolists\Components\TextEntry::make('reference')->fontFamily('mono')->copyable(),
                Infolists\Components\TextEntry::make('name')->columnSpan(2),
                Infolists\Components\TextEntry::make('category')->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => Asset::categoryOptions()[$state] ?? $state),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'active' => 'success','in_repair' => 'warning','retired' => 'gray','disposed' => 'danger', default => 'gray',
                    }),
                Infolists\Components\TextEntry::make('brand')->placeholder('—'),
                Infolists\Components\TextEntry::make('model')->placeholder('—'),
                Infolists\Components\TextEntry::make('serial_number')->label('Serial #')->placeholder('—'),
                Infolists\Components\TextEntry::make('location')->placeholder('—'),
                Infolists\Components\TextEntry::make('assignee.name')->label('Assigned To')->placeholder('Unassigned'),
                Infolists\Components\TextEntry::make('warranty_expires')->label('Warranty Expires')->date('d M Y')->placeholder('—'),
            ]),
            Infolists\Components\Section::make('Financial')->columns(4)->schema([
                Infolists\Components\TextEntry::make('purchase_date')->date('d M Y')->placeholder('—'),
                Infolists\Components\TextEntry::make('purchase_price')
                    ->getStateUsing(fn ($record) => $record->currency.' '.number_format((float)$record->purchase_price,0)),
                Infolists\Components\TextEntry::make('current_value')
                    ->label('Current Value')
                    ->getStateUsing(fn ($record) => $record->currency.' '.number_format((float)$record->current_value,0)),
                Infolists\Components\TextEntry::make('currency'),
            ]),
            Infolists\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Infolists\Components\TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'name', 'serial_number'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'view'   => Pages\ViewAsset::route('/{record}'),
            'edit'   => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
