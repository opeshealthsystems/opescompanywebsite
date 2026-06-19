<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeeklyReviewResource\Pages;
use App\Models\Cohort;
use App\Models\WeeklyReview;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WeeklyReviewResource extends Resource
{
    protected static ?string $model = WeeklyReview::class;
    protected static ?string $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 14;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('cohort_id')
                ->label('Cohort')->options(fn () => Cohort::pluck('name', 'id'))
                ->searchable()->required()
                ->disabledOn('edit'),
            Forms\Components\DatePicker::make('week_start')
                ->label('Week start')->native(false)->required()
                ->helperText('Start of the review week (snapshot covers 7 days).')
                ->disabledOn('edit'),
            Forms\Components\Textarea::make('summary')->rows(4),
            Forms\Components\Textarea::make('action_items')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cohort.name')->label('Cohort')->searchable(),
                Tables\Columns\TextColumn::make('week_start')->date(),
                Tables\Columns\TextColumn::make('week_end')->date(),
                Tables\Columns\TextColumn::make('author.name')->label('Author')->placeholder('—'),
                Tables\Columns\TextColumn::make('generated_at')->dateTime(),
            ])
            ->defaultSort('week_start', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Review')->columns(2)->schema([
                Infolists\Components\TextEntry::make('cohort.name')->label('Cohort'),
                Infolists\Components\TextEntry::make('author.name')->label('Author')->placeholder('—'),
                Infolists\Components\TextEntry::make('week_start')->date(),
                Infolists\Components\TextEntry::make('week_end')->date(),
                Infolists\Components\TextEntry::make('summary')->columnSpanFull()->placeholder('—'),
                Infolists\Components\TextEntry::make('action_items')->columnSpanFull()->placeholder('—'),
            ]),
            Infolists\Components\Section::make('Frozen Metrics')
                ->description('Snapshot taken when this review was generated.')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('metrics.sessions')->label('Sessions'),
                    Infolists\Components\TextEntry::make('metrics.issues_submitted')->label('Issues Submitted'),
                    Infolists\Components\TextEntry::make('metrics.retests.passed')->label('Retests Passed'),
                    Infolists\Components\TextEntry::make('metrics.retests.failed')->label('Retests Failed'),
                    Infolists\Components\TextEntry::make('metrics.dev_tasks_opened')->label('Dev Tasks Opened'),
                    Infolists\Components\TextEntry::make('metrics.dev_tasks_fixed')->label('Dev Tasks Fixed'),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWeeklyReviews::route('/'),
            'create' => Pages\CreateWeeklyReview::route('/create'),
            'view'   => Pages\ViewWeeklyReview::route('/{record}'),
            'edit'   => Pages\EditWeeklyReview::route('/{record}/edit'),
        ];
    }
}
