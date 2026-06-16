<?php
namespace App\Filament\Resources;

use App\Filament\Resources\RiskResource\Pages;
use App\Models\Risk;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RiskResource extends Resource
{
    protected static ?string $model = Risk::class;
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'Risk Register';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status','open')->where('risk_score','>=',12)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'danger'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Risk')->columns(2)->schema([
                Forms\Components\TextInput::make('reference')->disabled()->placeholder('Auto-generated'),
                Forms\Components\Select::make('status')->options(Risk::statusOptions())->default('open')->required(),
                Forms\Components\TextInput::make('title')->required()->maxLength(250)->columnSpanFull(),
                Forms\Components\Select::make('category')->options(Risk::categoryOptions())->required(),
                Forms\Components\Select::make('owner_id')
                    ->label('Risk Owner')
                    ->options(fn () => User::orderBy('name')->pluck('name','id'))
                    ->default(fn () => auth()->id())
                    ->searchable()->required(),
                Forms\Components\Select::make('likelihood')
                    ->options(Risk::likelihoodOptions())->default('medium')->required()->live()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                        $set('risk_score', Risk::computeScore($state, $get('impact') ?? 'medium'));
                    }),
                Forms\Components\Select::make('impact')
                    ->options(Risk::impactOptions())->default('medium')->required()->live()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                        $set('risk_score', Risk::computeScore($get('likelihood') ?? 'medium', $state));
                    }),
                Forms\Components\TextInput::make('risk_score')->label('Risk Score (auto)')->numeric()->disabled()->default(9),
                Forms\Components\DatePicker::make('review_date')->native(false)->nullable(),
            ]),
            Forms\Components\Section::make('Description & Mitigation')->schema([
                Forms\Components\Textarea::make('description')->rows(3)->nullable()->columnSpanFull(),
                Forms\Components\Textarea::make('mitigation_plan')->label('Mitigation Plan')->rows(3)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->fontFamily('mono')->copyable()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->weight('semibold'),
                Tables\Columns\TextColumn::make('category')->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => Risk::categoryOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('risk_score')->label('Score')->badge()
                    ->color(fn ($state) => match(true) {
                        $state >= 20 => 'danger', $state >= 12 => 'warning', $state >= 6 => 'info', default => 'success',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('likelihood')->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => ['very_low'=>'Very Low','low'=>'Low','medium'=>'Medium','high'=>'High','very_high'=>'Very High'][$state] ?? $state),
                Tables\Columns\TextColumn::make('impact')->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => ['very_low'=>'Very Low','low'=>'Low','medium'=>'Medium','high'=>'High','very_high'=>'Very High'][$state] ?? $state),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'open'=>'danger','mitigated'=>'warning','accepted'=>'info','closed'=>'success', default=>'gray',
                    }),
                Tables\Columns\TextColumn::make('owner.name')->label('Owner')->toggleable(),
                Tables\Columns\TextColumn::make('review_date')->date('d M Y')->placeholder('—')->toggleable(),
            ])
            ->defaultSort('risk_score','desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Risk::statusOptions()),
                Tables\Filters\SelectFilter::make('category')->options(Risk::categoryOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mitigate')
                    ->label('Mark Mitigated')->icon('heroicon-o-shield-check')->color('warning')
                    ->requiresConfirmation()
                    ->hidden(fn (Risk $record) => $record->status !== 'open')
                    ->action(fn (Risk $record) => $record->update(['status'=>'mitigated'])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Risk')->columns(3)->schema([
                Infolists\Components\TextEntry::make('reference')->fontFamily('mono')->copyable(),
                Infolists\Components\TextEntry::make('title')->columnSpan(2),
                Infolists\Components\TextEntry::make('category')->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => Risk::categoryOptions()[$state] ?? $state),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'open'=>'danger','mitigated'=>'warning','accepted'=>'info','closed'=>'success', default=>'gray',
                    }),
                Infolists\Components\TextEntry::make('owner.name')->label('Risk Owner'),
            ]),
            Infolists\Components\Section::make('Risk Assessment')->columns(3)->schema([
                Infolists\Components\TextEntry::make('likelihood')
                    ->formatStateUsing(fn ($state) => Risk::likelihoodOptions()[$state] ?? $state),
                Infolists\Components\TextEntry::make('impact')
                    ->formatStateUsing(fn ($state) => Risk::impactOptions()[$state] ?? $state),
                Infolists\Components\TextEntry::make('risk_score')->label('Risk Score')
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state >= 20 => 'danger', $state >= 12 => 'warning', $state >= 6 => 'info', default => 'success',
                    })
                    ->weight('bold'),
                Infolists\Components\TextEntry::make('review_date')->date('d M Y')->placeholder('—'),
            ]),
            Infolists\Components\Section::make('Details')->schema([
                Infolists\Components\TextEntry::make('description')->placeholder('—')->columnSpanFull(),
                Infolists\Components\TextEntry::make('mitigation_plan')->label('Mitigation Plan')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'title'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRisks::route('/'),
            'create' => Pages\CreateRisk::route('/create'),
            'view'   => Pages\ViewRisk::route('/{record}'),
            'edit'   => Pages\EditRisk::route('/{record}/edit'),
        ];
    }
}
