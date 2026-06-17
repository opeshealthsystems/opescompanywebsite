<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PractitionerFindingResource\Pages;
use App\Models\PractitionerFinding;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PractitionerFindingResource extends Resource
{
    protected static ?string $model = PractitionerFinding::class;
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Findings';
    protected static ?string $navigationGroup = 'Practitioners';
    protected static ?int    $navigationSort  = 4;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        $ratingOptions = array_combine(range(1, 5), range(1, 5));
        return $form->schema([
            Forms\Components\Section::make('Findings')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('application_id')
                        ->label('Application')
                        ->relationship('application', 'id')
                        ->searchable()
                        ->required(),
                    Forms\Components\Toggle::make('is_published')->label('Published'),
                    Forms\Components\Select::make('overall_rating')->options($ratingOptions)->nullable(),
                    Forms\Components\Select::make('wait_time_rating')->label('Wait Time Rating')->options($ratingOptions)->nullable(),
                    Forms\Components\Select::make('data_integrity_rating')->label('Data Integrity Rating')->options($ratingOptions)->nullable(),
                    Forms\Components\Select::make('usability_rating')->label('Usability Rating')->options($ratingOptions)->nullable(),
                    Forms\Components\TextInput::make('video_url')->label('Video URL')->url()->nullable()->columnSpanFull(),
                    Forms\Components\Textarea::make('findings_text')->label('Findings')->rows(6)->nullable()->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('practitioner.name')
                    ->label('Practitioner')
                    ->searchable(),
                Tables\Columns\TextColumn::make('application.program.title')
                    ->label('Programme')
                    ->limit(35)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('overall_rating')
                    ->label('Overall')
                    ->badge()
                    ->placeholder('—'),
                Tables\Columns\ImageColumn::make('screenshot_path')
                    ->label('Screenshot')
                    ->disk('public')
                    ->square()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')->label('Published'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_publish')
                    ->label(fn (PractitionerFinding $record) => $record->is_published ? 'Unpublish' : 'Publish')
                    ->icon('heroicon-o-eye')
                    ->action(function (PractitionerFinding $record) {
                        $record->update(['is_published' => !$record->is_published]);
                        Notification::make()
                            ->title($record->is_published ? 'Finding published' : 'Finding unpublished')
                            ->success()->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Ratings')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('overall_rating')->badge()->placeholder('—'),
                    Infolists\Components\TextEntry::make('wait_time_rating')->label('Wait Time')->badge()->placeholder('—'),
                    Infolists\Components\TextEntry::make('data_integrity_rating')->label('Data Integrity')->badge()->placeholder('—'),
                    Infolists\Components\TextEntry::make('usability_rating')->label('Usability')->badge()->placeholder('—'),
                    Infolists\Components\IconEntry::make('is_published')->label('Published')->boolean(),
                ]),
            Infolists\Components\Section::make('Findings')->schema([
                Infolists\Components\TextEntry::make('findings_text')->placeholder('—'),
                Infolists\Components\ViewEntry::make('video_url')
                    ->label('Video Review')
                    ->view('filament.infolists.finding-video-embed'),
                Infolists\Components\ImageEntry::make('screenshot_path')->label('Screenshot')->disk('public')->placeholder('—'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPractitionerFindings::route('/'),
            'view'  => Pages\ViewPractitionerFinding::route('/{record}'),
            'edit'  => Pages\EditPractitionerFinding::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['practitioner', 'application.program']);
    }
}
