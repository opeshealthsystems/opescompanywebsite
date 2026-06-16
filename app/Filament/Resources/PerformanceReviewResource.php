<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PerformanceReviewResource\Pages;
use App\Models\PerformanceReview;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PerformanceReviewResource extends Resource
{
    protected static ?string $model = PerformanceReview::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Performance Reviews';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status','draft')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'warning'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Review Details')->columns(2)->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Employee')
                    ->options(fn () => User::orderBy('name')->pluck('name','id'))
                    ->searchable()->required(),

                Forms\Components\Select::make('reviewer_id')
                    ->label('Reviewer')
                    ->options(fn () => User::whereHas('roles', fn ($q) =>
                        $q->whereIn('name',['super_admin','admin'])
                    )->orderBy('name')->pluck('name','id'))
                    ->default(fn () => auth()->id())
                    ->searchable()->required(),

                Forms\Components\TextInput::make('review_period')
                    ->required()->maxLength(50)->placeholder('e.g. Q1 2026 or H1 2026'),

                Forms\Components\DatePicker::make('review_date')
                    ->required()->default(now())->native(false),

                Forms\Components\Select::make('status')
                    ->options(PerformanceReview::statusOptions())->default('draft')->required(),
            ]),

            Forms\Components\Section::make('Ratings (1-5)')->columns(3)->schema([
                Forms\Components\Select::make('overall_rating')->label('Overall')
                    ->options(PerformanceReview::ratingOptions())->default(3)->required(),
                Forms\Components\Select::make('goals_rating')->label('Goals Achievement')
                    ->options(PerformanceReview::ratingOptions())->default(3)->required(),
                Forms\Components\Select::make('teamwork_rating')->label('Teamwork')
                    ->options(PerformanceReview::ratingOptions())->default(3)->required(),
                Forms\Components\Select::make('technical_rating')->label('Technical Skills')
                    ->options(PerformanceReview::ratingOptions())->default(3)->required(),
                Forms\Components\Select::make('communication_rating')->label('Communication')
                    ->options(PerformanceReview::ratingOptions())->default(3)->required(),
            ]),

            Forms\Components\Section::make('Feedback')->schema([
                Forms\Components\Textarea::make('strengths')->rows(3)->nullable()->columnSpanFull(),
                Forms\Components\Textarea::make('areas_for_improvement')->label('Areas for Improvement')->rows(3)->nullable()->columnSpanFull(),
                Forms\Components\Textarea::make('goals_for_next_period')->label('Goals for Next Period')->rows(3)->nullable()->columnSpanFull(),
                Forms\Components\Textarea::make('employee_comments')->label('Employee Comments')->rows(2)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')->label('Employee')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('review_period')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('review_date')->label('Date')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('overall_rating')->label('Overall')
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state >= 4 => 'success', $state == 3 => 'info', default => 'warning',
                    })
                    ->formatStateUsing(fn ($state) => $state.'/5'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'acknowledged' => 'success','submitted' => 'info','draft' => 'warning', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('reviewer.name')->label('Reviewer')->toggleable(),
            ])
            ->defaultSort('review_date','desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(PerformanceReview::statusOptions()),
                Tables\Filters\SelectFilter::make('user_id')->label('Employee')
                    ->options(fn () => User::orderBy('name')->pluck('name','id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('submit')
                    ->label('Submit')->icon('heroicon-o-paper-airplane')->color('info')
                    ->requiresConfirmation()
                    ->hidden(fn (PerformanceReview $record) => $record->status !== 'draft')
                    ->action(fn (PerformanceReview $record) => $record->update(['status'=>'submitted'])),
                Tables\Actions\Action::make('acknowledge')
                    ->label('Acknowledge')->icon('heroicon-o-check-badge')->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (PerformanceReview $record) => $record->status !== 'submitted')
                    ->action(fn (PerformanceReview $record) => $record->update(['status'=>'acknowledged','acknowledged_at'=>now()])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Review')->columns(3)->schema([
                Infolists\Components\TextEntry::make('employee.name')->label('Employee'),
                Infolists\Components\TextEntry::make('reviewer.name')->label('Reviewer'),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'acknowledged' => 'success','submitted' => 'info','draft' => 'warning', default => 'gray',
                    }),
                Infolists\Components\TextEntry::make('review_period'),
                Infolists\Components\TextEntry::make('review_date')->date('d M Y'),
                Infolists\Components\TextEntry::make('acknowledged_at')->label('Acknowledged At')->dateTime('d M Y H:i')->placeholder('—'),
            ]),
            Infolists\Components\Section::make('Ratings')->columns(5)->schema([
                Infolists\Components\TextEntry::make('overall_rating')->label('Overall')
                    ->badge()->color(fn ($state) => $state >= 4 ? 'success' : ($state == 3 ? 'info' : 'warning'))
                    ->formatStateUsing(fn ($state) => $state.'/5'),
                Infolists\Components\TextEntry::make('goals_rating')->label('Goals')
                    ->formatStateUsing(fn ($state) => $state.'/5'),
                Infolists\Components\TextEntry::make('teamwork_rating')->label('Teamwork')
                    ->formatStateUsing(fn ($state) => $state.'/5'),
                Infolists\Components\TextEntry::make('technical_rating')->label('Technical')
                    ->formatStateUsing(fn ($state) => $state.'/5'),
                Infolists\Components\TextEntry::make('communication_rating')->label('Communication')
                    ->formatStateUsing(fn ($state) => $state.'/5'),
            ]),
            Infolists\Components\Section::make('Feedback')->schema([
                Infolists\Components\TextEntry::make('strengths')->placeholder('—')->columnSpanFull(),
                Infolists\Components\TextEntry::make('areas_for_improvement')->label('Areas for Improvement')->placeholder('—')->columnSpanFull(),
                Infolists\Components\TextEntry::make('goals_for_next_period')->label('Goals for Next Period')->placeholder('—')->columnSpanFull(),
                Infolists\Components\TextEntry::make('employee_comments')->label('Employee Comments')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['review_period'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPerformanceReviews::route('/'),
            'create' => Pages\CreatePerformanceReview::route('/create'),
            'view'   => Pages\ViewPerformanceReview::route('/{record}'),
            'edit'   => Pages\EditPerformanceReview::route('/{record}/edit'),
        ];
    }
}
