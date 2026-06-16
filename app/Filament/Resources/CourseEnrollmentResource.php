<?php
namespace App\Filament\Resources;

use App\Filament\Resources\CourseEnrollmentResource\Pages;
use App\Models\CourseEnrollment;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CourseEnrollmentResource extends Resource
{
    protected static ?string $model = CourseEnrollment::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Student')->searchable()->sortable(),
                TextColumn::make('course.title')->label('Course')->searchable()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'enrolled'    => 'gray',
                        'in_progress' => 'info',
                        'completed'   => 'success',
                        'dropped'     => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => CourseEnrollment::statusOptions()[$state] ?? $state),
                TextColumn::make('progress')
                    ->label('Progress')
                    ->getStateUsing(fn (CourseEnrollment $record) => $record->progressPercent() . '%'),
                TextColumn::make('enrolled_at')->dateTime()->sortable(),
                TextColumn::make('completed_at')->dateTime()->default('—'),
            ])
            ->filters([
                SelectFilter::make('status')->options(CourseEnrollment::statusOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('enrolled_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseEnrollments::route('/'),
            'view'  => Pages\ViewCourseEnrollment::route('/{record}'),
        ];
    }
}
