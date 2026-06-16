<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimesheetResource\Pages;
use App\Models\Project;
use App\Models\Timesheet;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Timesheets';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Time Entry')->columns(2)->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Staff Member')
                    ->options(fn () => User::orderBy('name')->pluck('name','id'))
                    ->default(fn () => auth()->id())
                    ->searchable()->required(),

                Forms\Components\DatePicker::make('date')
                    ->required()->default(now())->native(false),

                Forms\Components\Select::make('project_id')
                    ->label('Project')
                    ->options(fn () => Project::whereIn('status',['active','planning'])->orderBy('title')->pluck('title','id'))
                    ->searchable()->nullable()->placeholder('No project'),

                Forms\Components\TextInput::make('hours')
                    ->numeric()->required()->minValue(0.25)->maxValue(24)->step(0.25)->suffix('hrs'),

                Forms\Components\Toggle::make('is_billable')->label('Billable')->default(false),

                Forms\Components\Textarea::make('description')
                    ->label('Work Description')->rows(2)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Staff')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('date')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('project.title')->label('Project')->placeholder('—')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('hours')->suffix(' hrs')->sortable(),
                Tables\Columns\IconColumn::make('is_billable')->label('Billable')->boolean(),
                Tables\Columns\TextColumn::make('description')->label('Description')->limit(50)->placeholder('—')->toggleable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')->label('Staff')
                    ->options(fn () => User::orderBy('name')->pluck('name','id')),
                Tables\Filters\SelectFilter::make('project_id')->label('Project')
                    ->relationship('project', 'title'),
                Tables\Filters\TernaryFilter::make('is_billable')->label('Billable'),
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
            Infolists\Components\Section::make('Time Entry')->columns(3)->schema([
                Infolists\Components\TextEntry::make('user.name')->label('Staff Member'),
                Infolists\Components\TextEntry::make('date')->date('d M Y'),
                Infolists\Components\TextEntry::make('hours')->suffix(' hrs')->weight('bold'),
                Infolists\Components\TextEntry::make('project.title')->label('Project')->placeholder('No project'),
                Infolists\Components\IconEntry::make('is_billable')->label('Billable')->boolean(),
                Infolists\Components\TextEntry::make('description')->label('Work Description')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['description'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTimesheets::route('/'),
            'create' => Pages\CreateTimesheet::route('/create'),
            'view'   => Pages\ViewTimesheet::route('/{record}'),
            'edit'   => Pages\EditTimesheet::route('/{record}/edit'),
        ];
    }
}
