<?php
namespace App\Filament\Resources;

use App\Filament\Resources\SlaRuleResource\Pages;
use App\Models\SlaRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SlaRuleResource extends Resource
{
    protected static ?string $model = SlaRule::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'SLA Rules';
    protected static ?string $navigationGroup = 'Support';
    protected static ?int $navigationSort = 35;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('SLA Rule')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(150)->columnSpanFull(),

                Forms\Components\Select::make('ticket_priority')
                    ->label('Ticket Priority')
                    ->options(SlaRule::priorityOptions())
                    ->required(),

                Forms\Components\Select::make('ticket_type')
                    ->label('Ticket Type (optional)')
                    ->options([
                        'support'      => 'Support',
                        'billing'      => 'Billing',
                        'technical'    => 'Technical',
                        'bug_report'   => 'Bug Report',
                        'other'        => 'Other',
                    ])
                    ->nullable()
                    ->placeholder('All types'),

                Forms\Components\TextInput::make('response_time_hours')
                    ->label('Response Time (hours)')
                    ->numeric()->required()->default(24)->minValue(1)->suffix('hrs'),

                Forms\Components\TextInput::make('resolution_time_hours')
                    ->label('Resolution Time (hours)')
                    ->numeric()->required()->default(72)->minValue(1)->suffix('hrs'),

                Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->weight('semibold'),
                Tables\Columns\TextColumn::make('ticket_priority')->badge()
                    ->color(fn ($state) => match($state) {
                        'urgent'=>'danger','high'=>'warning','medium'=>'info','low'=>'gray', default=>'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('ticket_type')
                    ->label('Type')->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => $state ? ucfirst(str_replace('_',' ',$state)) : 'All Types')
                    ->placeholder('All Types'),
                Tables\Columns\TextColumn::make('response_time_hours')->label('Response')->suffix(' hrs')->sortable(),
                Tables\Columns\TextColumn::make('resolution_time_hours')->label('Resolution')->suffix(' hrs')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->defaultSort('ticket_priority')
            ->filters([
                Tables\Filters\SelectFilter::make('ticket_priority')->label('Priority')
                    ->options(SlaRule::priorityOptions()),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
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
            Infolists\Components\Section::make('SLA Rule')->columns(2)->schema([
                Infolists\Components\TextEntry::make('name')->columnSpanFull()->weight('semibold'),
                Infolists\Components\TextEntry::make('ticket_priority')->badge()
                    ->color(fn ($state) => match($state) {
                        'urgent'=>'danger','high'=>'warning','medium'=>'info','low'=>'gray', default=>'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Infolists\Components\TextEntry::make('ticket_type')->label('Ticket Type')
                    ->formatStateUsing(fn ($state) => $state ? ucfirst(str_replace('_',' ',$state)) : 'All Types'),
                Infolists\Components\TextEntry::make('response_time_hours')->label('Response SLA')->suffix(' hours'),
                Infolists\Components\TextEntry::make('resolution_time_hours')->label('Resolution SLA')->suffix(' hours'),
                Infolists\Components\IconEntry::make('is_active')->label('Active')->boolean(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSlaRules::route('/'),
            'create' => Pages\CreateSlaRule::route('/create'),
            'view'   => Pages\ViewSlaRule::route('/{record}'),
            'edit'   => Pages\EditSlaRule::route('/{record}/edit'),
        ];
    }
}
