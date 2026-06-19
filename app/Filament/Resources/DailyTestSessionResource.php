<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyTestSessionResource\Pages;
use App\Models\DailyTestSession;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DailyTestSessionResource extends Resource
{
    protected static ?string $model = DailyTestSession::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\TextColumn::make('cohortMember.user.name')->label('Practitioner'),
                Tables\Columns\TextColumn::make('cohortMember.cohort.name')->label('Cohort'),
                Tables\Columns\TextColumn::make('product.name')->label('Product'),
                Tables\Columns\TextColumn::make('module.name')->label('Module'),
                Tables\Columns\TextColumn::make('workflow.name')->label('Workflow'),
                Tables\Columns\TextColumn::make('tasks_completed'),
                Tables\Columns\TextColumn::make('issue_reports_count')->counts('issueReports')->label('Issues'),
            ])
            ->defaultSort('date', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Session')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('date')->date(),
                    Infolists\Components\TextEntry::make('cohortMember.user.name')->label('Practitioner')->placeholder('—'),
                    Infolists\Components\TextEntry::make('cohortMember.cohort.name')->label('Cohort')->placeholder('—'),
                    Infolists\Components\TextEntry::make('product.name')->label('Product')->placeholder('—'),
                    Infolists\Components\TextEntry::make('module.name')->label('Module')->placeholder('—'),
                    Infolists\Components\TextEntry::make('workflow.name')->label('Workflow')->placeholder('—'),
                    Infolists\Components\TextEntry::make('facility_context')->placeholder('—'),
                    Infolists\Components\TextEntry::make('start_time')->placeholder('—'),
                    Infolists\Components\TextEntry::make('end_time')->placeholder('—'),
                    Infolists\Components\TextEntry::make('tasks_completed'),
                ]),
            Infolists\Components\Section::make('Notes')
                ->schema([
                    Infolists\Components\TextEntry::make('comments')->columnSpanFull()->placeholder('—'),
                    Infolists\Components\ImageEntry::make('screenshots')->placeholder('—')->columnSpanFull(),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyTestSessions::route('/'),
            'view'  => Pages\ViewDailyTestSession::route('/{record}'),
        ];
    }
}
