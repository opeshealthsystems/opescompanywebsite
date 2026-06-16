<?php
namespace App\Filament\Resources;

use App\Filament\Resources\CourseCertificateResource\Pages;
use App\Models\CourseCertificate;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CourseCertificateResource extends Resource
{
    protected static ?string $model = CourseCertificate::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('certificate_number')->label('Certificate #')->fontFamily('mono')->searchable(),
                TextColumn::make('user.name')->label('Student')->searchable()->sortable(),
                TextColumn::make('course.title')->label('Course')->searchable()->sortable(),
                TextColumn::make('issued_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('download_pdf')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (CourseCertificate $record) => route('certificates.pdf', ['locale' => app()->getLocale(), 'certificate' => $record->id]))
                    ->openUrlInNewTab()
                    ->visible(fn () => \Illuminate\Support\Facades\Route::has('certificates.pdf')),
            ])
            ->defaultSort('issued_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseCertificates::route('/'),
            'view'  => Pages\ViewCourseCertificate::route('/{record}'),
        ];
    }
}
