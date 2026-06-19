<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvisoryCouncilMemberResource\Pages;
use App\Models\AdvisoryCouncilMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdvisoryCouncilMemberResource extends Resource
{
    protected static ?string $model = AdvisoryCouncilMember::class;
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Advisory Council';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 17;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\DatePicker::make('term_start')->native(false)->required(),
            Forms\Components\DatePicker::make('term_end')->native(false),
            Forms\Components\Select::make('status')->options(AdvisoryCouncilMember::statusOptions())->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Member')->searchable(),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('term_start')->date(),
                Tables\Columns\TextColumn::make('term_end')->date()->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => AdvisoryCouncilMember::statusOptions()[$state] ?? $state)
                    ->color(fn ($state) => $state === 'active' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('invitedBy.name')->label('Invited by')->placeholder('—'),
            ])
            ->defaultSort('invited_at', 'desc')
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Council Member')->columns(2)->schema([
                Infolists\Components\TextEntry::make('user.name')->label('Member'),
                Infolists\Components\TextEntry::make('title'),
                Infolists\Components\TextEntry::make('term_start')->date(),
                Infolists\Components\TextEntry::make('term_end')->date()->placeholder('—'),
                Infolists\Components\TextEntry::make('status')->badge(),
                Infolists\Components\TextEntry::make('invitedBy.name')->label('Invited by')->placeholder('—'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdvisoryCouncilMembers::route('/'),
            'view'  => Pages\ViewAdvisoryCouncilMember::route('/{record}'),
            'edit'  => Pages\EditAdvisoryCouncilMember::route('/{record}/edit'),
        ];
    }
}
